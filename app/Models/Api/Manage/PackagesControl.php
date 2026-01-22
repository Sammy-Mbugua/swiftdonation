<?php

namespace App\Models\Api\Manage;

use Illuminate\Database\Eloquent\Model;

class PackagesControl extends Model
{
    // Todo: Manage Packages
    public static function manage_packages(array $packagesInfo, ?int $packageId = null): int
    {
        // thumbnail
        if (array_key_exists('thumbnail', $packagesInfo)) {
            $thumbnail = \App\Models\Auto::uploadFile(
                $packagesInfo['thumbnail'],
                "package",
                randomize_file_name: true,
                year_folder: true,
                private_upload: false
            );
            $packagesInfo['thumbnail'] = $thumbnail;
        }

        // Code
        $verification = new \App\Services\Verification();


        if (!$packageId) {
            // Random Number
            $code = 'PKG-' . $verification->generateRandomNumbers(5);
            // Code
            $packagesInfo['code'] = $code;
        }
        
        // Save Package
        $this_package = \App\Models\Cash\Package::updateOrCreate(['id' => $packageId], $packagesInfo);
        // dd($this_package);
        // Define fields that need JSON encoding
        return $this_package->id;
    }

    // Todo: Get Packages
    public static function get_packages(int $packageId): array
    {
        // Get Package
        $package = \App\Models\Cash\Package::find($packageId);

        // Return Package
        $results = $package?->toArray();

        if (!$results) {
            return  [];
        }

        return $results;
    }
}
