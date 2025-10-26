<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    /**
     * 获取设置值
     */
    public static function get($key, $default = null)
    {
        $cacheKey = "setting.{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * 设置值
     */
    public static function set($key, $value, $type = 'string', $group = 'general', $description = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );

        // 清除缓存
        Cache::forget("setting.{$key}");
        
        return $setting;
    }

    /**
     * 获取分组设置
     */
    public static function getGroup($group)
    {
        $cacheKey = "setting.group.{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            $settings = self::where('group', $group)->get();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = self::castValue($setting->value, $setting->type);
            }
            
            return $result;
        });
    }

    /**
     * 批量设置
     */
    public static function setGroup($group, $data)
    {
        foreach ($data as $key => $value) {
            self::set($key, $value, 'string', $group);
        }

        // 清除分组缓存
        Cache::forget("setting.group.{$group}");
    }

    /**
     * 类型转换
     */
    private static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
            case 'integer':
                return is_numeric($value) ? (int) $value : 0;
            case 'float':
                return is_numeric($value) ? (float) $value : 0.0;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * 清除所有设置缓存
     */
    public static function clearCache()
    {
        $keys = self::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting.{$key}");
        }
        
        $groups = self::distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("setting.group.{$group}");
        }
    }
}
