import paho.mqtt.client as mqtt
import json
import requests
import re
from datetime import datetime

# MQTT Config
MQTT_HOST = "203.194.115.76"
MQTT_PORT = 1883
MQTT_USER = "iot"
MQTT_PASS = "GANTI_DENGAN_PASSWORD_ANDA"

# Laravel API Base URL (for updating cache/status)
LARAVEL_BASE = "http://localhost:9000"

# Topics to subscribe
TOPICS = [
    # === Smart GH (legacy) ===
    "smartgh01/pub",
    "smartgh01/sub",
    # === Smart Farm (Irigasi Multi-Zona) ===
    "/irigasi1/pub",
    "/irigasi1/sub",
    "/irigasi1/status",
    "/irigasi1/log",
]

def on_connect(client, userdata, flags, rc):
    if rc == 0:
        print("✅ Connected to MQTT Broker!")
        print(f"   Host: {MQTT_HOST}:{MQTT_PORT}")
        print()
        
        # Subscribe to all topics
        for topic in TOPICS:
            client.subscribe(topic)
            print(f"📡 Subscribed to: {topic}")
        
        print()
        print("👂 Listening for messages... (Press Ctrl+C to stop)")
        print("─" * 60)
    else:
        print(f"❌ Connection failed with code {rc}")

def on_message(client, userdata, msg):
    timestamp = datetime.now().strftime("%H:%M:%S")
    topic = msg.topic
    payload = msg.payload.decode('utf-8').strip()
    
    print(f"\n[{timestamp}] 📨 Topic: {topic}")
    print(f"           Raw: {payload}")
    
    # === SMART FARM: Parse CMD protocol responses ===
    if payload.startswith("OK:") or payload.startswith("ERR:") or payload.startswith("EVT:"):
        parse_smart_farm_message(payload, topic, timestamp)
    
    # === LEGACY: Parse <dat|{JSON}|> format ===
    elif payload.startswith("<dat|") and payload.endswith("|>"):
        try:
            json_str = payload[5:-2]  # Remove <dat| and |>
            data = json.loads(json_str)
            print(f"           Parsed JSON:")
            for key, value in data.items():
                print(f"           • {key}: {value}")
        except json.JSONDecodeError:
            print("           ⚠️  Failed to parse JSON")
    
    # Log to file
    with open("mqtt_log.txt", "a") as f:
        f.write(f"[{datetime.now()}] {topic} | {payload}\n")

def parse_smart_farm_message(payload, topic, timestamp):
    """
    Parse Smart Farm protocol messages:
    
    Responses: OK:RELAY:0:1, OK:JADWAL_SET:0, OK:SIRAM_START:2, etc.
    Errors:    ERR:POMPA_TANPA_BLOK, ERR:SEDANG_SIRAM, ERR:UNKNOWN:xxx
    Events:    EVT:STATUS:siram=1:blok=2:sisa=1230:pupuk=ON:jam=0630:hari=3:error=0
               EVT:AUTO_OFF:POMPA:blok_habis
               EVT:RSSI:-65
    """
    parts = payload.split(":", 2)
    prefix = parts[0]  # OK, ERR, EVT
    
    if prefix == "EVT":
        handle_event(payload, parts, topic, timestamp)
    elif prefix == "OK":
        handle_ok_response(payload, parts, topic, timestamp)
    elif prefix == "ERR":
        handle_error(payload, parts, topic, timestamp)

def handle_event(payload, parts, topic, timestamp):
    """Handle EVT: messages from device"""
    if len(parts) < 2:
        return
    
    event_type = parts[1]
    
    # EVT:STATUS:siram=0:blok=0:sisa=0000:pupuk=NONE:jam=0630:hari=3:error=0
    if event_type == "STATUS":
        status_data = parse_status_string(payload)
        if status_data:
            print(f"    📊 Status Update:")
            print(f"       Siram: {'🟢 Aktif' if status_data.get('siram') else '⚪ Tidak'}")
            print(f"       Blok: {status_data.get('blok', '-')}")
            print(f"       Sisa: {status_data.get('sisa', '-')}")
            print(f"       Pupuk: {status_data.get('pupuk', '-')}")
            print(f"       Jam: {status_data.get('jam', '-')}")
            print(f"       Hari: {status_data.get('hari', '-')}")
            print(f"       Error: {'🔴' if status_data.get('error') else '🟢 Aman'}")
            
            # Update output cache based on status
            update_smart_farm_cache(topic, status_data)
    
    # EVT:AUTO_OFF:POMPA:blok_habis
    elif event_type == "AUTO_OFF":
        rest = parts[2] if len(parts) > 2 else ""
        auto_parts = rest.split(":")
        relay_name = auto_parts[0] if len(auto_parts) > 0 else "?"
        reason = auto_parts[1] if len(auto_parts) > 1 else "?"
        print(f"    ⚡ Auto OFF: {relay_name} → alasan: {reason}")
    
    # EVT:RSSI:-65
    elif event_type == "RSSI":
        rssi = parts[2] if len(parts) > 2 else "?"
        # Convert to 0-100 scale: constrain(rssi + 100, 0, 100)
        try:
            rssi_val = int(rssi)
            signal = max(0, min(100, rssi_val + 100))
            bars = "█" * (signal // 20) + "░" * (5 - signal // 20)
            print(f"    📶 RSSI: {rssi} dBm ({signal}%) [{bars}]")
        except ValueError:
            print(f"    📶 RSSI: {rssi}")

def handle_ok_response(payload, parts, topic, timestamp):
    """Handle OK: responses"""
    if len(parts) < 2:
        return
    
    cmd = parts[1]
    detail = parts[2] if len(parts) > 2 else ""
    
    if cmd == "PING":
        print(f"    ✅ Pong! Device connected")
    elif cmd == "RELAY":
        relay_info = detail.split(":")
        coil = relay_info[0] if len(relay_info) > 0 else "?"
        state = relay_info[1] if len(relay_info) > 1 else "?"
        coil_names = {0: "Pompa", 1: "Blok 1", 2: "Blok 2", 3: "Blok 3", 4: "Pupuk"}
        name = coil_names.get(int(coil), f"Coil {coil}")
        state_text = "🟢 ON" if state == "1" else "🔴 OFF"
        print(f"    ✅ Relay: {name} → {state_text}")
    elif cmd == "JADWAL_SET":
        print(f"    ✅ Jadwal #{detail} berhasil disimpan")
    elif cmd == "JADWAL_DEL":
        print(f"    ✅ Jadwal #{detail} berhasil dihapus")
    elif cmd == "SIRAM_START":
        print(f"    ✅ Penyiraman jadwal #{detail} dimulai")
    elif cmd == "SIRAM_STOP":
        print(f"    ✅ Penyiraman dihentikan")
    elif cmd == "RESET_ERROR":
        print(f"    ✅ Error relay direset")
    elif cmd == "SET_RTC":
        print(f"    ✅ Waktu RTC berhasil diset")
    elif cmd == "WIFI":
        print(f"    📶 WiFi: {detail}")
    elif cmd == "STATUS":
        status_data = parse_status_string(payload)
        if status_data:
            print(f"    ✅ Status: {status_data}")
    elif cmd.startswith("JADWAL"):
        # OK:JADWAL:<idx>:<jam>:<menit>:<durasi>:<blok>:<literPupuk10>:<hari_bitmask>:<aktif>
        if cmd == "JADWAL":
            jadwal_parts = detail.split(":")
            if len(jadwal_parts) >= 7:
                idx, jam, menit, durasi, blok, pupuk10, hari, aktif = (
                    jadwal_parts[0], jadwal_parts[1], jadwal_parts[2],
                    jadwal_parts[3], jadwal_parts[4], jadwal_parts[5],
                    jadwal_parts[6], jadwal_parts[7] if len(jadwal_parts) > 7 else "1"
                )
                pupuk_l = int(pupuk10) / 10 if pupuk10.isdigit() else 0
                status = "✅" if aktif == "1" else "⏸️"
                print(f"    📋 Jadwal #{idx}: {jam}:{menit:>02s} | {durasi}min | Blok {blok} | Pupuk {pupuk_l}L | {status}")
        elif detail == "JADWAL_END":
            print(f"    📋 === Akhir daftar jadwal ===")

def handle_error(payload, parts, topic, timestamp):
    """Handle ERR: responses"""
    err_type = parts[1] if len(parts) > 1 else "UNKNOWN"
    detail = parts[2] if len(parts) > 2 else ""
    
    error_messages = {
        "SEDANG_SIRAM": "❌ Tidak bisa mulai: penyiraman sedang berjalan",
        "JADWAL_INVALID": "❌ Index jadwal tidak valid / nonaktif",
        "TIDAK_SIRAM": "❌ Tidak ada penyiraman yang berjalan",
        "POMPA_TANPA_BLOK": "❌ Pompa tidak bisa ON: tidak ada blok yang aktif",
        "PUPUK_TANPA_POMPA": "❌ Pupuk tidak bisa ON: pompa belum ON",
        "PUPUK_TANPA_BLOK": "❌ Pupuk tidak bisa ON: tidak ada blok yang aktif",
        "RELAY_FORMAT": "❌ Format perintah relay salah",
        "JADWAL_FORMAT": "❌ Format jadwal salah",
        "IDX_INVALID": "❌ Index jadwal tidak valid",
        "RTC_FORMAT": "❌ Format set RTC salah",
    }
    
    msg = error_messages.get(err_type, f"❌ Error: {err_type}")
    if detail:
        msg += f" ({detail})"
    print(f"    {msg}")

def parse_status_string(status_line):
    """
    Parse status string:
    EVT:STATUS:siram=1:blok=2:sisa=1230:pupuk=ON:jam=0630:hari=3:error=0
    """
    # Remove prefix
    line = re.sub(r'^(EVT|OK):STATUS:', '', status_line)
    if not line:
        return None
    
    result = {}
    parts = line.split(':')
    for part in parts:
        if '=' in part:
            key, value = part.split('=', 1)
            try:
                result[key] = int(value)
            except ValueError:
                result[key] = value
    return result

def update_smart_farm_cache(topic, status_data):
    """
    Update Laravel cache with Smart Farm output states based on EVT:STATUS data.
    Sends HTTP POST to Laravel internal API to update DeviceOutput cache & DB.
    """
    siram = status_data.get('siram', 0)
    blok  = status_data.get('blok', 0)
    pupuk = status_data.get('pupuk', 'NONE')
    sisa  = status_data.get('sisa', 0)
    error = status_data.get('error', 0)

    # Map status ke output_name
    outputs = {
        'sf_pompa': 1 if siram else 0,
        'sf_blok1': 1 if (siram and blok == 1) else 0,
        'sf_blok2': 1 if (siram and blok == 2) else 0,
        'sf_blok3': 1 if (siram and blok == 3) else 0,
        'sf_pupuk': 1 if pupuk == 'ON' else 0,
    }

    print(f"    💾 Output states: {outputs}")

    # Kirim ke Laravel internal API
    try:
        payload = {
            'topic': topic,
            'outputs': outputs,
            'siram': siram,
            'blok': blok,
            'pupuk': pupuk,
            'sisa': sisa,
            'error': error,
        }
        resp = requests.post(
            f"{LARAVEL_BASE}/api/internal/smart-farm/status",
            json=payload,
            timeout=3,
            headers={'Accept': 'application/json'}
        )
        if resp.status_code == 200:
            print(f"    ✅ Laravel cache updated")
        else:
            print(f"    ⚠️  Laravel API returned {resp.status_code}: {resp.text[:100]}")
    except requests.exceptions.ConnectionError:
        print(f"    ⚠️  Laravel not reachable (offline mode - outputs only logged)")
    except Exception as e:
        print(f"    ⚠️  Error calling Laravel API: {e}")

def main():
    print("🚀 Starting MQTT Listener (Smart GH + Smart Farm)...")
    print()
    
    client = mqtt.Client()
    client.username_pw_set(MQTT_USER, MQTT_PASS)
    client.on_connect = on_connect
    client.on_message = on_message
    
    try:
        client.connect(MQTT_HOST, MQTT_PORT, 60)
        client.loop_forever()
    except KeyboardInterrupt:
        print("\n\n👋 Stopping listener...")
        client.disconnect()
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    main()
