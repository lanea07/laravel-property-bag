<?php

namespace LaravelPropertyBag\Helpers;

use Illuminate\Container\Container;

class NameResolver
{
    /**
     * Get the app namespace from the container.
     *
     * @return string
     */
    public static function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }

    /**
     * Make config file name for resource.
     *
     * @param string $resourceName
     *
     * @return string
     */
    public static function makeConfigFileName($classOrModel): string
    {
        $appNamespace = static::getAppNamespace();

        if (is_object($classOrModel)) {
            // Old behavior: based on model
            $reflection = new \ReflectionClass($classOrModel);
            $modelNamespace = $reflection->getNamespaceName();
            $modelShortName = $reflection->getShortName();

            // Remove app namespace and 'Models\' from model namespace
            $relativeNamespace = ltrim(str_replace(
                [$appNamespace . 'Models\\', $appNamespace],
                '',
                $modelNamespace
            ), '\\');

            $settingsNamespace = $appNamespace . 'Settings';
            if ($relativeNamespace) {
                $settingsNamespace .= '\\' . $relativeNamespace;
            }

            $fqcn = $settingsNamespace . '\\' . $modelShortName . 'Settings';
        } else {
            // If only short class name is passed
            $shortName = (string) $classOrModel;

            // Try direct root under App\Settings
            $fqcn = $appNamespace . 'Settings\\' . $shortName;

            if (!class_exists($fqcn)) {
                // 🔎 Optionally: search recursively under App\Settings
                $fqcn = self::findInSettingsNamespace($shortName, $appNamespace . 'Settings');
            }
        }

        return $fqcn;
    }

    /**
     * Search recursively under the Settings namespace for a class.
     *
     * @param string $shortName
     * @param string $baseNamespace
     *
     * @return string|null
     */
    protected static function findInSettingsNamespace(string $shortName, string $baseNamespace): ?string
    {
        $settingsPath = realpath(app_path('Settings'));
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($settingsPath)
        );

        foreach ($iterator as $file) {
            if (
                $file->isFile() &&
                $file->getExtension() === 'php' &&
                $file->getBasename('.php') === $shortName . 'Settings'
            ) {
                $filePath = realpath($file->getPath());

                $relativePath = trim(str_replace($settingsPath, '', $filePath), DIRECTORY_SEPARATOR);

                $relativeNamespace = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

                $fqcn = rtrim($baseNamespace . '\\' . $relativeNamespace, '\\') . '\\' . $shortName . 'Settings';

                if (class_exists($fqcn)) {
                    return $fqcn;
                }
            }
        }

        return null;
    }

    /**
     * Make rules file name.
     *
     * @return string
     */
    public static function makeRulesFileName()
    {
        $appNamespace = static::getAppNamespace();

        return $appNamespace . 'Settings\\Resources\\Rules';
    }
}
