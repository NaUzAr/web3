# SmartAgri IoT - Complete MQTT Keys Reference

## 📡 Format Data dari ESP32
Semua data dikirim dengan wrapper: `<dat|{JSON}|>`

---

## 1️⃣ SENSOR DATA (Counter 1) - Disimpan ke Database
| Key | Deskripsi | Satuan |
|-----|-----------|--------|
| `ni_PH` | Nilai pH | - |
| `ni_EC` | Electrical Conductivity | mS/cm |
| `ni_TDS` | Total Dissolved Solids | ppm |
| `ni_LUX` | Intensitas Cahaya | lux |
| `ni_SUHU` | Suhu/Temperature | °C |
| `ni_KELEM` | Kelembaban/Humidity | % |

**Contoh:**
```json
<dat|{"ni_PH":6.8,"ni_EC":1200,"ni_TDS":850,"ni_LUX":1500,"ni_SUHU":28.5,"ni_KELEM":65}|>
```

---

## 2️⃣ SCHEDULE DATA (Counter 2 & 3) - Log Only
| Key | Deskripsi |
|-----|-----------|
| `sch1` | Jadwal 1 (timestamp) |
| `sch2` | Jadwal 2 |
| `sch3` | Jadwal 3 |
| `sch4` | Jadwal 4 |
| `sch5` | Jadwal 5 |
| `sch6` | Jadwal 6 |
| `sch7` | Jadwal 7 |
| `sch8` | Jadwal 8 |
| `sch9` | Jadwal 9 |
| `sch10` | Jadwal 10 |
| `sch11` | Jadwal 11 |
| `sch12` | Jadwal 12 |
| `sch13` | Jadwal 13 |
| `sch14` | Jadwal 14 |

---

## 3️⃣ THRESHOLD DATA (Counter 4) - Log Only
| Key | Deskripsi |
|-----|-----------|
| `bts_ats_suhu` | Batas Atas Suhu |
| `bts_bwh_suhu` | Batas Bawah Suhu |
| `bts_ats_kelem` | Batas Atas Kelembaban |
| `bts_bwh_kelem` | Batas Bawah Kelembaban |
| `bts_ats_ph` | Batas Atas pH |
| `bts_bwh_ph` | Batas Bawah pH |
| `bts_ats_tds` | Batas Atas TDS |
| `bts_bwh_tds` | Batas Bawah TDS |

---

## 4️⃣ MODE DATA (Counter 5) - Log Only
| Key | Deskripsi | Nilai |
|-----|-----------|-------|
| `mode_dos` | Mode Dosing | 0/1 |
| `mode_clim` | Mode Climate | 0/1 |

---

## 5️⃣ STATUS OUTPUT (Counter 6) - Log Only
| Key | Label | Nilai |
|-----|-------|-------|
| `sts_air_input` | Air Input | 0/1 |
| `sts_mixing` | Mixing | 0/1 |
| `sts_pompa` | Pompa | 0/1 |
| `sts_fan` | Fan | 0/1 |
| `sts_misting` | Misting | 0/1 |
| `sts_lampu` | Lampu | 0/1 |
| `sts_dosing` | Dosing | 0/1 |
| `sts_ph_up` | pH Up | 0/1 |
| `sts_air_baku` | Air Baku | 0/1 |
| `sts_air_pupuk` | Air Pupuk | 0/1 |
| `sts_ph_down` | pH Down | 0/1 |

---

## 6️⃣ TIME DATA (Counter 7) - Log Only
| Key | Deskripsi |
|-----|-----------|
| `waktu` | Timestamp device |

---

## 📊 AVAILABLE SENSOR TYPES (Konfigurasi Admin)
| Key DB | Label | Satuan |
|--------|-------|--------|
| `temperature` | Suhu (Temperature) | °C |
| `humidity` | Kelembaban (Humidity) | % |
| `rainfall` | Curah Hujan (Rainfall) | mm |
| `wind_speed` | Kecepatan Angin | km/h |
| `wind_direction` | Arah Angin | ° |
| `pressure` | Tekanan Udara | hPa |
| `uv_index` | Indeks UV | - |
| `light_intensity` | Intensitas Cahaya | lux |
| `soil_moisture` | Kelembaban Tanah | % |
| `soil_ph` | pH Tanah | - |
| `soil_temperature` | Suhu Tanah | °C |
| `water_level` | Level Air | cm |
| `co2` | CO2 | ppm |
| `ec` | EC (Electrical Conductivity) | mS/cm |
| `tds` | TDS (Total Dissolved Solids) | ppm |
| `ph` | pH Air | - |

---

## 🔌 AVAILABLE OUTPUT TYPES (Konfigurasi Admin)
| Key | Label | Tipe |
|-----|-------|------|
| `relay` | Relay | boolean |
| `pump` | Pompa Air | boolean |
| `fan` | Kipas/Fan | boolean |
| `valve` | Katup/Valve | boolean |
| `motor` | Motor Speed | percentage |
| `led` | LED | boolean |
| `buzzer` | Buzzer | boolean |
| `servo` | Servo Motor | number |
| `heater` | Pemanas/Heater | boolean |
| `sprinkler` | Sprinkler | boolean |

---

## 🔧 FORMAT KONTROL (Web → Device)

### Manual Output Control
Topic: `{mqtt_topic}/control`
```
<output_name#value>
```
Contoh: `<pump#1>` atau `<fan#0>`

---

## 📌 SUMMARY

| Kategori | Jumlah Keys |
|----------|-------------|
| Sensor Data | 6 keys |
| Schedule | 14 keys |
| Threshold | 8 keys |
| Mode | 2 keys |
| Status Output | 11 keys |
| Time | 1 key |
| **TOTAL ESP32 Keys** | **42 keys** |
| Available Sensor Types | 16 types |
| Available Output Types | 10 types |

---

## 🌾 SMART FARM PROTOCOL (STM32 Multi-Zona)
Untuk dokumentasi lengkap protokol Smart Farm (CMD:BLOK, CMD:RELAY, CMD:RELAY_STATUS, EVT:AUTO_OFF, dan logika penentuan mode Otomatis vs Manual), lihat:
👉 [SMART_FARM_RELAY_STATUS_SPEC.md](file:///d:/Dev/SWATANI/web2/docs/SMART_FARM_RELAY_STATUS_SPEC.md)

