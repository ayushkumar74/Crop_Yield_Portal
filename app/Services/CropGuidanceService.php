<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class CropGuidanceService
{
    /**
     * Get the complete structured agronomic profile for a specific crop.
     */
    public function getCropProfile(string $cropName): array
    {
        $locale = App::getLocale();
        $profiles = $this->getProfiles();

        // Match crop name (case insensitive)
        $normalizedName = strtolower(trim(str_replace(' (Corn)', '', $cropName)));

        $defaultProfile = $profiles['default'][$locale] ?? $profiles['default']['en'];

        if (isset($profiles[$normalizedName])) {
            return $profiles[$normalizedName][$locale] ?? $profiles[$normalizedName]['en'];
        }

        return $defaultProfile;
    }

    /**
     * Get the base difficulty (Low, Medium, High) for the crop.
     */
    public function getDifficulty(string $cropName): string
    {
        $normalizedName = strtolower(trim(str_replace(' (Corn)', '', $cropName)));
        $difficulties = [
            'rice' => 'Medium',
            'wheat' => 'Low',
            'maize' => 'Low',
            'cotton' => 'High',
            'soybean' => 'Medium',
            'sugarcane' => 'Medium',
            'potato' => 'Medium',
            'tomato' => 'High',
            'chickpea' => 'Low',
            'mustard' => 'Low',
            'sunflower' => 'Medium',
            'groundnut' => 'Medium',
        ];

        return $difficulties[$normalizedName] ?? 'Medium';
    }

    /**
     * Get estimated market price per metric ton in Indian Rupees (INR).
     * These values reflect general Indian wholesale market equivalents.
     */
    public function getMarketPricePerTon(string $cropName): float
    {
        $normalizedName = strtolower(trim(str_replace(' (Corn)', '', $cropName)));
        $prices = [
            'rice' => 29000.0,      // ~₹29,000/ton (MSP/Market avg)
            'wheat' => 24000.0,     // ~₹24,000/ton
            'maize' => 21000.0,     // ~₹21,000/ton
            'cotton' => 75000.0,    // ~₹75,000/ton (varies by staple length)
            'soybean' => 48000.0,   // ~₹48,000/ton
            'sugarcane' => 3800.0,  // ~₹3,800/ton (FRP/SAP)
            'potato' => 12000.0,    // ~₹12,000/ton
            'tomato' => 20000.0,    // ~₹20,000/ton
            'chickpea' => 58000.0,  // ~₹58,000/ton
            'mustard' => 54000.0,   // ~₹54,000/ton
            'sunflower' => 48000.0, // ~₹48,000/ton
            'groundnut' => 65000.0, // ~₹65,000/ton
        ];

        return $prices[$normalizedName] ?? 25000.0; // Default generic crop price
    }

    private function getProfiles(): array
    {
        return [
            'rice' => [
                'en' => [
                    'sowing_season' => 'Kharif (June - July)',
                    'irrigation_schedule' => 'Continuous submergence (5cm) until 15 days before harvest. Critical stages: Tillering, Panicle initiation.',
                    'fertilizer_recommendations' => 'Apply N in 3 splits (basal, tillering, panicle initiation). Apply full P and K as basal.',
                    'npk_guidance' => '120:60:40 kg/ha',
                    'soil_preparation' => 'Puddling is essential to reduce water percolation and control weeds. Level the field perfectly.',
                    'disease_pest' => 'Pests: Stem borer, Brown plant hopper. Diseases: Blast, Bacterial leaf blight. Use resistant varieties.',
                    'harvest_duration' => '30-40 days after flowering',
                    'maturity_time' => '120 - 150 days',
                    'profitability' => 'Medium to High (depending on MSP and variety like Basmati)',
                    'market_demand' => 'Consistently high domestic and export demand.',
                    'yield_tips' => 'Maintain exact water level. Timely weed control in the first 30 days is critical.',
                    'weather_risks' => 'Highly susceptible to drought during flowering; sensitive to extreme heat > 35°C.',
                    'water_requirement' => 'High (1200-1500 mm)',
                    'maintenance_difficulty' => 'Medium',
                    'suitable_regions' => 'Punjab, West Bengal, UP, Andhra Pradesh, Tamil Nadu',
                    'organic_suggestions' => 'Use Azolla and Blue Green Algae as bio-fertilizers. Incorporate green manure (Dhaincha) before planting.',
                ],
                'hi' => [
                    'sowing_season' => 'खरीफ (जून - जुलाई)',
                    'irrigation_schedule' => 'कटाई से 15 दिन पहले तक निरंतर जल-भराव (5 सेमी)। महत्वपूर्ण चरण: कल्ले निकलना, बालियां आना।',
                    'fertilizer_recommendations' => 'नाइट्रोजन को 3 भागों में दें। P और K की पूरी मात्रा बुवाई के समय दें।',
                    'npk_guidance' => '120:60:40 किग्रा/हेक्टेयर',
                    'soil_preparation' => 'पानी के रिसाव को रोकने और खरपतवार नियंत्रण के लिए खेत की पडलिंग (कीचड़ करना) आवश्यक है। खेत को समतल करें।',
                    'disease_pest' => 'कीट: तना छेदक, भूरा फुदका। रोग: ब्लास्ट, झुलसा। प्रतिरोधी किस्मों का प्रयोग करें।',
                    'harvest_duration' => 'फूल आने के 30-40 दिन बाद',
                    'maturity_time' => '120 - 150 दिन',
                    'profitability' => 'मध्यम से उच्च (MSP और किस्म पर निर्भर)',
                    'market_demand' => 'घरेलू और निर्यात मांग लगातार उच्च।',
                    'yield_tips' => 'पानी का सही स्तर बनाए रखें। पहले 30 दिनों में समय पर खरपतवार नियंत्रण महत्वपूर्ण है।',
                    'weather_risks' => 'फूल आने के दौरान सूखे के प्रति अत्यधिक संवेदनशील; 35°C से अधिक गर्मी हानिकारक है।',
                    'water_requirement' => 'उच्च (1200-1500 मिमी)',
                    'maintenance_difficulty' => 'मध्यम',
                    'suitable_regions' => 'पंजाब, पश्चिम बंगाल, यूपी, आंध्र प्रदेश, तमिलनाडु',
                    'organic_suggestions' => 'जैव उर्वरक के रूप में अजोला और नील हरित शैवाल का उपयोग करें। बुवाई से पहले हरी खाद (ढैंचा) मिलाएं।',
                ],
            ],
            'wheat' => [
                'en' => [
                    'sowing_season' => 'Rabi (November - December)',
                    'irrigation_schedule' => '4-6 irrigations. Critical stages: Crown root initiation (CRI), jointing, milking, and dough stages.',
                    'fertilizer_recommendations' => 'Half N and full P & K at sowing. Remaining N at first irrigation (CRI stage).',
                    'npk_guidance' => '120:60:40 kg/ha',
                    'soil_preparation' => 'Deep ploughing followed by 2-3 harrowing to get fine tilth. Ensure good moisture at sowing.',
                    'disease_pest' => 'Pests: Termites, Aphids. Diseases: Rust (Yellow/Brown), Loose smut.',
                    'harvest_duration' => '10-15 days when grain moisture is 15-20%',
                    'maturity_time' => '120 - 150 days',
                    'profitability' => 'Medium (Stable due to MSP procurement)',
                    'market_demand' => 'High staple demand globally and domestically.',
                    'yield_tips' => 'Timely sowing is crucial. Delay reduces yield by 30-40 kg/ha/day. Ensure CRI stage irrigation.',
                    'weather_risks' => 'Terminal heat stress in March can shrivel grains. Vulnerable to unseasonal rain/hail at harvest.',
                    'water_requirement' => 'Medium (450-650 mm)',
                    'maintenance_difficulty' => 'Low',
                    'suitable_regions' => 'Punjab, Haryana, MP, UP, Rajasthan',
                    'organic_suggestions' => 'Use FYM (Farm Yard Manure) or vermicompost. Treat seeds with Trichoderma viride.',
                ],
                'hi' => [
                    'sowing_season' => 'रबी (नवंबर - दिसंबर)',
                    'irrigation_schedule' => '4-6 सिंचाई। महत्वपूर्ण चरण: ताज मूल (CRI), गांठे बनना, दूधिया अवस्था।',
                    'fertilizer_recommendations' => 'बुवाई के समय आधा N और पूरा P, K दें। शेष N पहली सिंचाई (CRI) पर दें।',
                    'npk_guidance' => '120:60:40 किग्रा/हेक्टेयर',
                    'soil_preparation' => 'गहरी जुताई के बाद 2-3 बार हैरो चलाकर भुरभुरी मिट्टी तैयार करें।',
                    'disease_pest' => 'कीट: दीमक, माहू। रोग: रतुआ (पीला/भूरा), लूज स्मट।',
                    'harvest_duration' => 'दाने में नमी 15-20% होने पर 10-15 दिन',
                    'maturity_time' => '120 - 150 दिन',
                    'profitability' => 'मध्यम (MSP खरीद के कारण स्थिर)',
                    'market_demand' => 'घरेलू और वैश्विक स्तर पर मुख्य भोजन की उच्च मांग।',
                    'yield_tips' => 'समय पर बुवाई बहुत महत्वपूर्ण है। देरी से उपज घटती है। CRI अवस्था पर सिंचाई सुनिश्चित करें।',
                    'weather_risks' => 'मार्च में अचानक गर्मी बढ़ने से दाने सिकुड़ सकते हैं। कटाई के समय ओलावृष्टि से जोखिम।',
                    'water_requirement' => 'मध्यम (450-650 मिमी)',
                    'maintenance_difficulty' => 'निम्न',
                    'suitable_regions' => 'पंजाब, हरियाणा, मध्य प्रदेश, यूपी, राजस्थान',
                    'organic_suggestions' => 'FYM या वर्मीकम्पोस्ट का प्रयोग करें। बीजों को ट्राइकोडर्मा विरिडी से उपचारित करें।',
                ],
            ],
            'cotton' => [
                'en' => [
                    'sowing_season' => 'Kharif (April - July depending on region)',
                    'irrigation_schedule' => 'Avoid excess water early. Critical stages: Square formation, flowering, and boll development.',
                    'fertilizer_recommendations' => 'Apply N in 3-4 splits. Basal application of P and K is mandatory.',
                    'npk_guidance' => '150:75:75 kg/ha',
                    'soil_preparation' => 'Requires deep ploughing. Soil must have good drainage. Form ridges and furrows.',
                    'disease_pest' => 'Pests: Pink bollworm, Whitefly, Aphids. Diseases: Leaf curl virus, Verticillium wilt.',
                    'harvest_duration' => 'Multiple pickings over 1-2 months',
                    'maturity_time' => '150 - 180 days',
                    'profitability' => 'High (Cash crop, subject to global market rates)',
                    'market_demand' => 'Strong industrial demand for textiles and oil.',
                    'yield_tips' => 'Maintain weed-free field for first 60 days. Timely pest scouting is critical for bollworm.',
                    'weather_risks' => 'Extremely sensitive to waterlogging. Heavy rain during boll opening ruins quality.',
                    'water_requirement' => 'Medium to High (700-1200 mm)',
                    'maintenance_difficulty' => 'High',
                    'suitable_regions' => 'Gujarat, Maharashtra, Telangana, Punjab, Haryana',
                    'organic_suggestions' => 'Use trap crops like Marigold. Employ pheromone traps and Neem Seed Kernel Extract (NSKE) for pests.',
                ],
                'hi' => [
                    'sowing_season' => 'खरीफ (अप्रैल - जुलाई क्षेत्रानुसार)',
                    'irrigation_schedule' => 'शुरुआत में अधिक पानी से बचें। महत्वपूर्ण: फूल आना और डोडी (boll) विकास।',
                    'fertilizer_recommendations' => 'N को 3-4 भागों में दें। P और K बुवाई के समय ही दें।',
                    'npk_guidance' => '150:75:75 किग्रा/हेक्टेयर',
                    'soil_preparation' => 'गहरी जुताई की आवश्यकता। जल निकासी अच्छी होनी चाहिए। मेड़ और नालियां बनाएं।',
                    'disease_pest' => 'कीट: गुलाबी सुंडी (Pink bollworm), सफेद मक्खी। रोग: लीफ कर्ल वायरस।',
                    'harvest_duration' => '1-2 महीने तक कई बार चुनाई',
                    'maturity_time' => '150 - 180 दिन',
                    'profitability' => 'उच्च (नकदी फसल, वैश्विक बाजार पर निर्भर)',
                    'market_demand' => 'कपड़ा और तेल उद्योग के लिए भारी मांग।',
                    'yield_tips' => 'पहले 60 दिनों तक खेत को खरपतवार मुक्त रखें। कीटों की नियमित निगरानी आवश्यक है।',
                    'weather_risks' => 'जलभराव के प्रति अत्यंत संवेदनशील। डोडी खुलने पर बारिश से गुणवत्ता खराब होती है।',
                    'water_requirement' => 'मध्यम से उच्च (700-1200 मिमी)',
                    'maintenance_difficulty' => 'उच्च',
                    'suitable_regions' => 'गुजरात, महाराष्ट्र, तेलंगाना, पंजाब, हरियाणा',
                    'organic_suggestions' => 'गेंदा जैसी ट्रैप फसलें लगाएं। फेरोमोन ट्रैप और नीम के अर्क (NSKE) का प्रयोग करें।',
                ],
            ],
            'sugarcane' => [
                'en' => [
                    'sowing_season' => 'Autumn (Oct-Nov) or Spring (Feb-Mar)',
                    'irrigation_schedule' => 'Requires frequent irrigation (10-15 days interval in summer). Critical stages: Formative and grand growth.',
                    'fertilizer_recommendations' => 'Heavy feeder. Apply N in 3-4 splits before grand growth stage.',
                    'npk_guidance' => '250:100:100 kg/ha',
                    'soil_preparation' => 'Deep ploughing (25-30 cm) is essential. Form deep furrows.',
                    'disease_pest' => 'Pests: Early shoot borer, Pyrilla. Diseases: Red rot, Smut, Wilt.',
                    'harvest_duration' => 'Extended harvest period when Brix value reaches >18',
                    'maturity_time' => '10 - 18 months',
                    'profitability' => 'High (Cash crop, stable FRP/SAP pricing)',
                    'market_demand' => 'Very high demand for sugar, ethanol, and jaggery.',
                    'yield_tips' => 'Earthing up is crucial to prevent lodging. Ensure proper drainage to avoid waterlogging.',
                    'weather_risks' => 'Frost can severely damage cane. Drought reduces sucrose accumulation.',
                    'water_requirement' => 'Very High (1500-2500 mm)',
                    'maintenance_difficulty' => 'Medium',
                    'suitable_regions' => 'UP, Maharashtra, Karnataka, Tamil Nadu, Gujarat',
                    'organic_suggestions' => 'Use press mud, compost, and bio-fertilizers like Acetobacter. Practice trash mulching to conserve moisture.',
                ],
                'hi' => [
                    'sowing_season' => 'शरदकालीन (अक्टूबर-नवंबर) या वसंतकालीन (फरवरी-मार्च)',
                    'irrigation_schedule' => 'गर्मियों में हर 10-15 दिन में सिंचाई। महत्वपूर्ण: प्रारंभिक और मुख्य विकास अवस्था।',
                    'fertilizer_recommendations' => 'अधिक उर्वरक चाहिए। N को 3-4 भागों में मुख्य विकास अवस्था से पहले दें।',
                    'npk_guidance' => '250:100:100 किग्रा/हेक्टेयर',
                    'soil_preparation' => 'गहरी जुताई (25-30 सेमी) आवश्यक। गहरी नालियां (furrows) बनाएं।',
                    'disease_pest' => 'कीट: तना छेदक, पायरिला। रोग: लाल सड़न (Red rot), स्मट।',
                    'harvest_duration' => 'ब्रिक्स (Brix) मान >18 होने पर लंबी कटाई अवधि',
                    'maturity_time' => '10 - 18 महीने',
                    'profitability' => 'उच्च (FRP/SAP मूल्य निर्धारण के कारण स्थिर)',
                    'market_demand' => 'चीनी, इथेनॉल और गुड़ के लिए भारी मांग।',
                    'yield_tips' => 'फसल को गिरने से बचाने के लिए मिट्टी चढ़ाना (Earthing up) आवश्यक है।',
                    'weather_risks' => 'पाला (Frost) गन्ने को भारी नुकसान पहुंचाता है। सूखे से सुक्रोज कम होता है।',
                    'water_requirement' => 'बहुत उच्च (1500-2500 मिमी)',
                    'maintenance_difficulty' => 'मध्यम',
                    'suitable_regions' => 'यूपी, महाराष्ट्र, कर्नाटक, तमिलनाडु, गुजरात',
                    'organic_suggestions' => 'प्रेस मड, खाद और एसीटोबैक्टर जैसे जैव-उर्वरक का उपयोग करें। नमी बचाने के लिए मल्चिंग करें।',
                ],
            ],
            // Adding a generic default mapping for any other crop
            'default' => [
                'en' => [
                    'sowing_season' => 'Refer to local agricultural guidelines.',
                    'irrigation_schedule' => 'Maintain optimal soil moisture. Irrigate at critical growth stages.',
                    'fertilizer_recommendations' => 'Apply basal dose of P and K. Split application for Nitrogen.',
                    'npk_guidance' => 'Based on soil test report (generally 100:50:50).',
                    'soil_preparation' => 'Plough 2-3 times for a fine seedbed. Ensure good drainage.',
                    'disease_pest' => 'Scout regularly for local pests. Use integrated pest management (IPM).',
                    'harvest_duration' => 'Harvest when crop reaches physiological maturity.',
                    'maturity_time' => '90 - 150 days (varies by variety)',
                    'profitability' => 'Medium to High',
                    'market_demand' => 'Steady demand in local and wholesale markets.',
                    'yield_tips' => 'Use certified seeds, maintain plant population, and control weeds early.',
                    'weather_risks' => 'Vulnerable to extreme temperature fluctuations and unseasonal rain.',
                    'water_requirement' => 'Medium',
                    'maintenance_difficulty' => 'Medium',
                    'suitable_regions' => 'Most arable regions with suitable climate.',
                    'organic_suggestions' => 'Use crop rotation, farmyard manure, and biopesticides like Neem oil.',
                ],
                'hi' => [
                    'sowing_season' => 'स्थानीय कृषि दिशा-निर्देशों का संदर्भ लें।',
                    'irrigation_schedule' => 'मिट्टी में इष्टतम नमी बनाए रखें। महत्वपूर्ण विकास चरणों में सिंचाई करें।',
                    'fertilizer_recommendations' => 'P और K बुवाई के समय दें। नाइट्रोजन को विभाजित करके दें।',
                    'npk_guidance' => 'मिट्टी परीक्षण रिपोर्ट के आधार पर (आमतौर पर 100:50:50)।',
                    'soil_preparation' => 'भुरभुरी मिट्टी के लिए 2-3 बार जुताई करें। अच्छी जल निकासी सुनिश्चित करें।',
                    'disease_pest' => 'नियमित रूप से कीटों की जांच करें। समेकित कीट प्रबंधन (IPM) अपनाएं।',
                    'harvest_duration' => 'फसल के शारीरिक रूप से परिपक्व होने पर कटाई करें।',
                    'maturity_time' => '90 - 150 दिन (किस्म पर निर्भर)',
                    'profitability' => 'मध्यम से उच्च',
                    'market_demand' => 'स्थानीय बाजारों में स्थिर मांग।',
                    'yield_tips' => 'प्रमाणित बीजों का उपयोग करें, और शुरुआत में खरपतवार नियंत्रण करें।',
                    'weather_risks' => 'अत्यधिक तापमान में उतार-चढ़ाव और बेमौसम बारिश के प्रति संवेदनशील।',
                    'water_requirement' => 'मध्यम',
                    'maintenance_difficulty' => 'मध्यम',
                    'suitable_regions' => 'उपयुक्त जलवायु वाले अधिकांश कृषि क्षेत्र।',
                    'organic_suggestions' => 'फसल चक्र, गोबर की खाद और नीम के तेल जैसे जैव-कीटनाशकों का प्रयोग करें।',
                ],
            ],
        ];
    }
}
