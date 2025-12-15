<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            // === Americas ===
            ['USD','United States Dollar','US', true],
            ['CAD','Canadian Dollar','CA', true],
            ['MXN','Mexican Peso','MX'],
            ['BRL','Brazilian Real','BR'],
            ['ARS','Argentine Peso','AR'],
            ['CLP','Chilean Peso','CL'],
            ['COP','Colombian Peso','CO'],
            ['PEN','Peruvian Sol','PE'],
            ['UYU','Uruguayan Peso','UY'],
            ['BOB','Bolivian Boliviano','BO'],
            ['VES','Venezuelan Bolívar','VE'],

            // === Europe ===
            ['EUR','Euro','EU', true],
            ['GBP','British Pound Sterling','GB', true],
            ['CHF','Swiss Franc','CH'],
            ['SEK','Swedish Krona','SE'],
            ['NOK','Norwegian Krone','NO'],
            ['DKK','Danish Krone','DK'],
            ['PLN','Polish Zloty','PL'],
            ['CZK','Czech Koruna','CZ'],
            ['HUF','Hungarian Forint','HU'],
            ['RON','Romanian Leu','RO'],
            ['BGN','Bulgarian Lev','BG'],
            ['HRK','Croatian Kuna','HR'],
            ['RSD','Serbian Dinar','RS'],
            ['UAH','Ukrainian Hryvnia','UA'],
            ['RUB','Russian Ruble','RU'],

            // === Asia ===
            ['IDR','Indonesian Rupiah','ID', true],
            ['JPY','Japanese Yen','JP', true],
            ['CNY','Chinese Yuan','CN', true],
            ['KRW','South Korean Won','KR'],
            ['SGD','Singapore Dollar','SG', true],
            ['MYR','Malaysian Ringgit','MY'],
            ['THB','Thai Baht','TH'],
            ['PHP','Philippine Peso','PH'],
            ['VND','Vietnamese Dong','VN'],
            ['INR','Indian Rupee','IN'],
            ['PKR','Pakistani Rupee','PK'],
            ['BDT','Bangladeshi Taka','BD'],
            ['LKR','Sri Lankan Rupee','LK'],
            ['NPR','Nepalese Rupee','NP'],
            ['KHR','Cambodian Riel','KH'],
            ['LAK','Lao Kip','LA'],
            ['MMK','Myanmar Kyat','MM'],

            // === Middle East ===
            ['AED','UAE Dirham','AE'],
            ['SAR','Saudi Riyal','SA'],
            ['QAR','Qatari Riyal','QA'],
            ['KWD','Kuwaiti Dinar','KW'],
            ['BHD','Bahraini Dinar','BH'],
            ['OMR','Omani Rial','OM'],
            ['ILS','Israeli New Shekel','IL'],
            ['IRR','Iranian Rial','IR'],
            ['IQD','Iraqi Dinar','IQ'],

            // === Africa ===
            ['ZAR','South African Rand','ZA'],
            ['NGN','Nigerian Naira','NG'],
            ['EGP','Egyptian Pound','EG'],
            ['KES','Kenyan Shilling','KE'],
            ['TZS','Tanzanian Shilling','TZ'],
            ['UGX','Ugandan Shilling','UG'],
            ['GHS','Ghanaian Cedi','GH'],
            ['MAD','Moroccan Dirham','MA'],
            ['XOF','West African CFA Franc','SN'],
            ['XAF','Central African CFA Franc','CM'],

            // === Oceania ===
            ['AUD','Australian Dollar','AU', true],
            ['NZD','New Zealand Dollar','NZ'],
            ['FJD','Fijian Dollar','FJ'],
            ['PGK','Papua New Guinea Kina','PG'],
        ];

        foreach ($currencies as $currency) {
            [$code, $name, $countryCode, $active] = array_pad($currency, 4, false);

            $flagPath = "flags/" . strtolower($countryCode) . ".png";

            // Download flag
            try {
                if (!Storage::disk('public')->exists($flagPath)) {
                    $response = Http::get("https://flagsapi.com/{$countryCode}/flat/64.png");
                    if ($response->successful()) {
                        Storage::disk('public')->put($flagPath, $response->body());
                    }
                }
            } catch (\Throwable $e) {
                $flagPath = null;
            }

            Currency::updateOrCreate(
                ['code' => $code],
                [
                    'name'         => $name,
                    'country_code' => $countryCode,
                    'flag'         => $flagPath,
                    'buy_rate'     => 0,
                    'sell_rate'    => 0,
                    'is_active'    => $active,
                ]
            );
        }
    }
}
