<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceOutput extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'output_name',
        'output_label',
        'output_type',
        'unit',
        'default_value',
        'current_value',
        'automation_mode',
        'max_schedules',
        'max_sectors',
        'automation_sensor_id'
    ];

    /**
     * Cast attributes to proper types
     */
    protected $casts = [
        'default_value' => 'float',
        'current_value' => 'float',
    ];

    /**
     * Get the customized icon based on output type/name
     */
    public function getIconAttribute()
    {
        $outLabel = strtolower($this->output_label);
        $outName = strtolower($this->output_name);

        if (str_contains($outLabel, 'ph')) return 'bi-speedometer2';
        if (str_contains($outLabel, 'mix') || str_contains($outLabel, 'dosing') || str_contains($outLabel, 'nutrisi') || str_contains($outLabel, 'ab')) return 'bi-droplet-half';
        if (str_contains($outLabel, 'pompa air') || str_contains($outLabel, 'water pump') || str_contains($outLabel, 'pompa utama') || str_contains($outLabel, 'pompa') || str_contains($outLabel, 'pump') || str_contains($outName, 'pump')) return 'bi-water';
        if (str_contains($outLabel, 'fan') || str_contains($outLabel, 'kipas') || str_contains($outLabel, 'blower') || str_contains($outLabel, 'exhaust') || str_contains($outName, 'fan')) return 'bi-fan';
        if (str_contains($outLabel, 'lamp') || str_contains($outLabel, 'cahaya') || str_contains($outLabel, 'light') || str_contains($outName, 'lamp')) return 'bi-lightbulb';
        if (str_contains($outLabel, 'mist') || str_contains($outLabel, 'kabut') || str_contains($outLabel, 'pengkabutan') || str_contains($outName, 'mist')) return 'bi-cloud-fog';
        if (str_contains($outLabel, 'heater') || str_contains($outLabel, 'pemanas')) return 'bi-fire';
        if (str_contains($outLabel, 'valve') || str_contains($outLabel, 'katup') || str_contains($outLabel, 'kran')) return 'bi-sign-stop';
        if (str_contains($outLabel, 'cool') || str_contains($outLabel, 'pendingin')) return 'bi-snow';
        if (str_contains($outLabel, 'shading net open')) return 'bi-chevron-double-up';
        if (str_contains($outLabel, 'shading net close')) return 'bi-chevron-double-down';
        if (str_contains($outLabel, 'shading') || str_contains($outLabel, 'net')) return 'bi-window-stack';
        
        $isStatusOnly = str_starts_with($this->output_name, 'sts_') || in_array($this->output_name, ['st_bak', 'st_ppk']);
        if ($isStatusOnly) return 'bi-info-circle';
        
        return 'bi-plug';
    }

    /**
     * Get the customized color based on output type/name
     */
    public function getColorAttribute()
    {
        $outLabel = strtolower($this->output_label);
        $outName = strtolower($this->output_name);

        if (str_contains($outLabel, 'ph')) return '#8E44AD';
        if (str_contains($outLabel, 'mix') || str_contains($outLabel, 'dosing') || str_contains($outLabel, 'nutrisi') || str_contains($outLabel, 'ab')) return '#F39C12';
        if (str_contains($outLabel, 'pompa air') || str_contains($outLabel, 'water pump') || str_contains($outLabel, 'pompa utama') || str_contains($outLabel, 'pompa') || str_contains($outLabel, 'pump') || str_contains($outName, 'pump')) return '#3498DB';
        if (str_contains($outLabel, 'fan') || str_contains($outLabel, 'kipas') || str_contains($outLabel, 'blower') || str_contains($outLabel, 'exhaust') || str_contains($outName, 'fan')) return '#95A5A6';
        if (str_contains($outLabel, 'lamp') || str_contains($outLabel, 'cahaya') || str_contains($outLabel, 'light') || str_contains($outName, 'lamp')) return '#F1C40F';
        if (str_contains($outLabel, 'mist') || str_contains($outLabel, 'kabut') || str_contains($outLabel, 'pengkabutan') || str_contains($outName, 'mist')) return '#5DADE2';
        if (str_contains($outLabel, 'heater') || str_contains($outLabel, 'pemanas')) return '#E74C3C';
        if (str_contains($outLabel, 'valve') || str_contains($outLabel, 'katup') || str_contains($outLabel, 'kran')) return '#1ABC9C';
        if (str_contains($outLabel, 'cool') || str_contains($outLabel, 'pendingin')) return '#00BCD4'; // Cyan
        if (str_contains($outLabel, 'shading net open')) return '#607D8B'; // Blue Grey
        if (str_contains($outLabel, 'shading net close')) return '#546E7A'; // Darker Blue Grey
        if (str_contains($outLabel, 'shading') || str_contains($outLabel, 'net')) return '#607D8B';
        
        $isStatusOnly = str_starts_with($this->output_name, 'sts_') || in_array($this->output_name, ['st_bak', 'st_ppk']);
        if ($isStatusOnly) return '#22c55e';
        
        return 'var(--primary-gradient)';
    }

    /**
     * Relasi ke Device
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Relasi ke OutputAutomationConfig
     */
    public function automationConfigs()
    {
        return $this->hasMany(OutputAutomationConfig::class, 'device_output_id');
    }

    /**
     * Relasi ke Sensor untuk Automation
     */
    public function automationSensor()
    {
        return $this->belongsTo(DeviceSensor::class, 'automation_sensor_id');
    }
}
