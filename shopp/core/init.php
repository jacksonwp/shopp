<?php

/**
 * Index of global region names */
function get_global_regions () {
	$regions = array();
	$regions[0] = "North America";
	$regions[1] = "Central America";
	$regions[2] = "South America";
	$regions[3] = "Europe";
	$regions[4] = "Middle East";
	$regions[5] = "Africa";
	$regions[6] = "Asia";
	$regions[7] = "Oceania";
	return $regions;
}

/**
 * Country data table
 * 20 KB in the database, load only when absolutely necessary and unset() asap */
function get_countries () {
	$countries = array();
	$countries['AF'] = array('name'=>'Afghanistan','currency'=>'AFA','region'=>4);
	$countries['AL'] = array('name'=>'Albania','currency'=>'ALL','region'=>3); 
	$countries['DZ'] = array('name'=>'Algeria','currency'=>'DZD','region'=>5); 
	$countries['AS'] = array('name'=>'American Samoa','currency'=>'USD','region'=>7); 
	$countries['AD'] = array('name'=>'Andorra','currency'=>'EUR','region'=>3); 
	$countries['AO'] = array('name'=>'Angola','currency'=>'AON','region'=>5); 
	$countries['AI'] = array('name'=>'Anguilla','currency'=>'XCD','region'=>0); 
	$countries['AQ'] = array('name'=>'Antarctica','currency'=>'ATA','region'=>2); 
	$countries['AG'] = array('name'=>'Antigua and Barbuda','currency'=>'XCD','region'=>0); 
	$countries['AR'] = array('name'=>'Argentina','currency'=>'ARS','region'=>2); 
	$countries['AM'] = array('name'=>'Armenia','currency'=>'AMD','region'=>3); 
	$countries['AW'] = array('name'=>'Aruba','currency'=>'AWG','region'=>0); 
	$countries['AU'] = array('name'=>'Australia','currency'=>'AUD','region'=>7); 
	$countries['AT'] = array('name'=>'Austria','currency'=>'EUR','region'=>3); 
	$countries['AZ'] = array('name'=>'Azerbaijan','currency'=>'AZM','region'=>6); 
	$countries['BS'] = array('name'=>'Bahamas','currency'=>'BSD','region'=>0); 
	$countries['BH'] = array('name'=>'Bahrain','currency'=>'BHD','region'=>4); 
	$countries['BD'] = array('name'=>'Bangladesh','currency'=>'BDT','region'=>6); 
	$countries['BB'] = array('name'=>'Barbados','currency'=>'BBD','region'=>0); 
	$countries['BY'] = array('name'=>'Belarus','currency'=>'BYB','region'=>3); 
	$countries['BE'] = array('name'=>'Belgium','currency'=>'EUR','region'=>3); 
	$countries['BZ'] = array('name'=>'Belize','currency'=>'BZD','region'=>1); 
	$countries['BJ'] = array('name'=>'Benin','currency'=>'XOF','region'=>5); 
	$countries['BM'] = array('name'=>'Bermuda','currency'=>'BMD','region'=>0); 
	$countries['BT'] = array('name'=>'Bhutan','currency'=>'BTN','region'=>6); 
	$countries['BO'] = array('name'=>'Bolivia','currency'=>'BOB','region'=>2); 
	$countries['BA'] = array('name'=>'Bosnia-Herzegovina','currency'=>'BAM','region'=>3); 
	$countries['BW'] = array('name'=>'Botswana','currency'=>'BWP','region'=>5); 
	$countries['BV'] = array('name'=>'Bouvet Island','currency'=>'NOK','region'=>5); 
	$countries['BR'] = array('name'=>'Brazil','currency'=>'BRL','region'=>2); 
	$countries['IO'] = array('name'=>'British Indian Ocean Territory','currency'=>'USD','region'=>7); 
	$countries['BN'] = array('name'=>'Brunei Darussalam','currency'=>'BND','region'=>6); 
	$countries['BG'] = array('name'=>'Bulgaria','currency'=>'BGL','region'=>3); 
	$countries['BF'] = array('name'=>'Burkina Faso','currency'=>'XOF','region'=>5); 
	$countries['BI'] = array('name'=>'Burundi','currency'=>'BIF','region'=>5); 
	$countries['KH'] = array('name'=>'Cambodia','currency'=>'KHR','region'=>6); 
	$countries['CM'] = array('name'=>'Cameroon','currency'=>'XAF','region'=>5); 
	$countries['CA'] = array('name'=>'Canada','currency'=>'CAD','region'=>0); 
	$countries['CV'] = array('name'=>'Cape Verde','currency'=>'CVE','region'=>5); 
	$countries['KY'] = array('name'=>'Cayman Islands','currency'=>'KYD','region'=>0); 
	$countries['CF'] = array('name'=>'Central African Republic','currency'=>'XAF','region'=>5); 
	$countries['TD'] = array('name'=>'Chad','currency'=>'XAF','region'=>5); 
	$countries['CL'] = array('name'=>'Chile','currency'=>'CLP','region'=>2); 
	$countries['CN'] = array('name'=>'China','currency'=>'CNY','region'=>6); 
	$countries['CX'] = array('name'=>'Christmas Island','currency'=>'AUD','region'=>7); 
	$countries['CC'] = array('name'=>'Cocos (Keeling) Islands','currency'=>'AUD','region'=>7); 
	$countries['CO'] = array('name'=>'Colombia','currency'=>'COP','region'=>2); 
	$countries['KM'] = array('name'=>'Comoros','currency'=>'KMF','region'=>5); 
	$countries['CG'] = array('name'=>'Congo','currency'=>'XAF','region'=>5); 
	$countries['CK'] = array('name'=>'Cook Islands','currency'=>'NZD','region'=>7); 
	$countries['CR'] = array('name'=>'Costa Rica','currency'=>'CRC','region'=>1); 
	$countries['HR'] = array('name'=>'Croatia','currency'=>'HRK','region'=>3); 
	$countries['CU'] = array('name'=>'Cuba','currency'=>'CUP','region'=>0); 
	$countries['CY'] = array('name'=>'Cyprus','currency'=>'CYP','region'=>3); 
	$countries['CZ'] = array('name'=>'Czech Republic','currency'=>'CZK','region'=>3); 
	$countries['CD'] = array('name'=>'Democratic Republic of Congo','currency'=>'CDF','region'=>5); 
	$countries['DK'] = array('name'=>'Denmark','currency'=>'DKK','region'=>3); 
	$countries['DE'] = array('name'=>'Deutschland','currency'=>'EUR','region'=>3); 
	$countries['DJ'] = array('name'=>'Djibouti','currency'=>'DJF','region'=>5); 
	$countries['DM'] = array('name'=>'Dominica','currency'=>'XCD','region'=>0); 
	$countries['DO'] = array('name'=>'Dominican Republic','currency'=>'DOP','region'=>0); 
	$countries['TP'] = array('name'=>'East Timor','currency'=>'TPE','region'=>6); 
	$countries['EC'] = array('name'=>'Ecuador','currency'=>'ECS','region'=>2); 
	$countries['EG'] = array('name'=>'Egypt','currency'=>'EGP','region'=>4); 
	$countries['SV'] = array('name'=>'El Salvador','currency'=>'SVC','region'=>1); 
	$countries['GQ'] = array('name'=>'Equatorial Guinea','currency'=>'XAF','region'=>5); 
	$countries['ER'] = array('name'=>'Eritrea','currency'=>'ERN','region'=>5); 
	$countries['EE'] = array('name'=>'Estonia','currency'=>'EEK','region'=>3); 
	$countries['ET'] = array('name'=>'Ethiopia','currency'=>'ETB','region'=>5); 
	$countries['FK'] = array('name'=>'Falkland Islands','currency'=>'FKP','region'=>2); 
	$countries['FO'] = array('name'=>'Faroe Islands','currency'=>'DKK','region'=>3); 
	$countries['FJ'] = array('name'=>'Fiji','currency'=>'FJD','region'=>7); 
	$countries['FI'] = array('name'=>'Finland','currency'=>'EUR','region'=>3); 
	$countries['FR'] = array('name'=>'France','currency'=>'EUR','region'=>3); 
	$countries['TF'] = array('name'=>'French Southern Territories','currency'=>'EUR','region'=>3); 
	$countries['GA'] = array('name'=>'Gabon','currency'=>'XAF','region'=>5); 
	$countries['GM'] = array('name'=>'Gambia','currency'=>'GMD','region'=>5); 
	$countries['GE'] = array('name'=>'Georgia','currency'=>'GEL','region'=>6); 
	$countries['DE'] = array('name'=>'Germany','currency'=>'EUR','region'=>3); 
	$countries['GH'] = array('name'=>'Ghana','currency'=>'GHC','region'=>5); 
	$countries['GI'] = array('name'=>'Gibraltar','currency'=>'GIP','region'=>3); 
	$countries['GB'] = array('name'=>'Great Britain','currency'=>'GBP','region'=>3); 
	$countries['GR'] = array('name'=>'Greece','currency'=>'EUR','region'=>3); 
	$countries['GL'] = array('name'=>'Greenland','currency'=>'DKK','region'=>0); 
	$countries['GD'] = array('name'=>'Grenada','currency'=>'XCD','region'=>0); 
	$countries['GP'] = array('name'=>'Guadeloupe (French)','currency'=>'EUR','region'=>3); 
	$countries['GU'] = array('name'=>'Guam (USA)','currency'=>'USD','region'=>7); 
	$countries['GT'] = array('name'=>'Guatemala','currency'=>'QTQ','region'=>1); 
	$countries['GF'] = array('name'=>'Guernsey','currency'=>'GBP','region'=>3); 
	$countries['GN'] = array('name'=>'Guinea','currency'=>'GNF','region'=>5); 
	$countries['GW'] = array('name'=>'Guinea-Bissau','currency'=>'GWP','region'=>5); 
	$countries['GY'] = array('name'=>'Guyana','currency'=>'GYD','region'=>2); 
	$countries['HT'] = array('name'=>'Haiti','currency'=>'HTG','region'=>0); 
	$countries['HM'] = array('name'=>'Heard Island and McDonald Islands','currency'=>'AUD','region'=>7); 
	$countries['HN'] = array('name'=>'Honduras','currency'=>'HNL','region'=>1); 
	$countries['HK'] = array('name'=>'Hong Kong','currency'=>'HKD','region'=>6); 
	$countries['HU'] = array('name'=>'Hungary','currency'=>'HUF','region'=>3); 
	$countries['IS'] = array('name'=>'Iceland','currency'=>'ISK','region'=>3); 
	$countries['IN'] = array('name'=>'India','currency'=>'INR','region'=>6); 
	$countries['ID'] = array('name'=>'Indonesia','currency'=>'IDR','region'=>7); 
	$countries['IR'] = array('name'=>'Iran','currency'=>'IRR','region'=>4); 
	$countries['IQ'] = array('name'=>'Iraq','currency'=>'IQD','region'=>4); 
	$countries['IE'] = array('name'=>'Ireland','currency'=>'EUR','region'=>3); 
	$countries['IM'] = array('name'=>'Isle of Man','currency'=>'GBP','region'=>3); 
	$countries['IL'] = array('name'=>'Israel','currency'=>'ILS','region'=>4); 
	$countries['IT'] = array('name'=>'Italy','currency'=>'EUR','region'=>3); 
	$countries['CI'] = array('name'=>'Ivory Coast','currency'=>'XOF','region'=>5); 
	$countries['JM'] = array('name'=>'Jamaica','currency'=>'JMD','region'=>0); 
	$countries['JP'] = array('name'=>'Japan','currency'=>'JPY','region'=>6); 
	$countries['JE'] = array('name'=>'Jersey','currency'=>'GBP','region'=>3); 
	$countries['JO'] = array('name'=>'Jordan','currency'=>'JOD','region'=>4); 
	$countries['KZ'] = array('name'=>'Kazakhstan','currency'=>'KZT','region'=>6); 
	$countries['KE'] = array('name'=>'Kenya','currency'=>'KES','region'=>5); 
	$countries['KI'] = array('name'=>'Kiribati','currency'=>'AUD','region'=>7); 
	$countries['KP'] = array('name'=>'Korea, North','currency'=>'KPW','region'=>6); 
	$countries['KR'] = array('name'=>'Korea, South','currency'=>'KRW','region'=>6); 
	$countries['KW'] = array('name'=>'Kuwait','currency'=>'KWD','region'=>4); 
	$countries['KG'] = array('name'=>'Kyrgyzstan','currency'=>'KGS','region'=>6); 
	$countries['LA'] = array('name'=>'Laos','currency'=>'LAK','region'=>6); 
	$countries['LV'] = array('name'=>'Latvia','currency'=>'LVL','region'=>3); 
	$countries['LB'] = array('name'=>'Lebanon','currency'=>'LBP','region'=>4); 
	$countries['LS'] = array('name'=>'Lesotho','currency'=>'LSL','region'=>5); 
	$countries['LR'] = array('name'=>'Liberia','currency'=>'LRD','region'=>5); 
	$countries['LY'] = array('name'=>'Libya','currency'=>'LYD','region'=>5); 
	$countries['LI'] = array('name'=>'Liechtenstein','currency'=>'CHF','region'=>3); 
	$countries['LT'] = array('name'=>'Lithuania','currency'=>'LTL','region'=>3); 
	$countries['LU'] = array('name'=>'Luxembourg','currency'=>'EUR','region'=>3); 
	$countries['MO'] = array('name'=>'Macau','currency'=>'MOP','region'=>6); 
	$countries['MK'] = array('name'=>'Macedonia','currency'=>'MKD','region'=>3); 
	$countries['MG'] = array('name'=>'Madagascar','currency'=>'MGF','region'=>5); 
	$countries['MW'] = array('name'=>'Malawi','currency'=>'MWK','region'=>5); 
	$countries['MY'] = array('name'=>'Malaysia','currency'=>'MYR','region'=>6); 
	$countries['MV'] = array('name'=>'Maldives','currency'=>'MVR','region'=>6); 
	$countries['ML'] = array('name'=>'Mali','currency'=>'XOF','region'=>5); 
	$countries['MT'] = array('name'=>'Malta','currency'=>'MTL','region'=>3); 
	$countries['MH'] = array('name'=>'Marshall Islands','currency'=>'USD','region'=>7); 
	$countries['MQ'] = array('name'=>'Martinique (French)','currency'=>'EUR','region'=>3); 
	$countries['MR'] = array('name'=>'Mauritania','currency'=>'MRO','region'=>5); 
	$countries['MU'] = array('name'=>'Mauritius','currency'=>'MUR','region'=>5); 
	$countries['YT'] = array('name'=>'Mayotte','currency'=>'EUR','region'=>3); 
	$countries['ME'] = array('name'=>'Montenegro','currency'=>'EUR','region'=>3); 
	$countries['MX'] = array('name'=>'Mexico','currency'=>'MXN','region'=>0); 
	$countries['FM'] = array('name'=>'Micronesia','currency'=>'USD','region'=>7); 
	$countries['MD'] = array('name'=>'Moldova','currency'=>'MDL','region'=>3); 
	$countries['MC'] = array('name'=>'Monaco','currency'=>'EUR','region'=>3); 
	$countries['MN'] = array('name'=>'Mongolia','currency'=>'MNT','region'=>6); 
	$countries['MS'] = array('name'=>'Montserrat','currency'=>'XCD','region'=>0); 
	$countries['MA'] = array('name'=>'Morocco','currency'=>'MAD','region'=>5); 
	$countries['MZ'] = array('name'=>'Mozambique','currency'=>'MZM','region'=>5); 
	$countries['MM'] = array('name'=>'Myanmar','currency'=>'MMK','region'=>6); 
	$countries['NA'] = array('name'=>'Namibia','currency'=>'NAD','region'=>5); 
	$countries['NR'] = array('name'=>'Nauru','currency'=>'AUD','region'=>7); 
	$countries['NP'] = array('name'=>'Nepal','currency'=>'NPR','region'=>6); 
	$countries['NL'] = array('name'=>'Netherlands','currency'=>'EUR','region'=>3); 
	$countries['AN'] = array('name'=>'Netherlands Antilles','currency'=>'ANG','region'=>0); 
	$countries['NC'] = array('name'=>'New Caledonia (French)','currency'=>'XPF','region'=>7); 
	$countries['NZ'] = array('name'=>'New Zealand','currency'=>'NZD','region'=>7); 
	$countries['NI'] = array('name'=>'Nicaragua','currency'=>'NIC','region'=>1); 
	$countries['NE'] = array('name'=>'Niger','currency'=>'XOF','region'=>5); 
	$countries['NG'] = array('name'=>'Nigeria','currency'=>'NGN','region'=>5); 
	$countries['NU'] = array('name'=>'Niue','currency'=>'NZD','region'=>7); 
	$countries['NF'] = array('name'=>'Norfolk Island','currency'=>'AUD','region'=>7); 
	$countries['MP'] = array('name'=>'Northern Mariana Islands','currency'=>'USD','region'=>7); 
	$countries['NO'] = array('name'=>'Norway','currency'=>'NOK','region'=>3); 
	$countries['OM'] = array('name'=>'Oman','currency'=>'OMR','region'=>4); 
	$countries['PK'] = array('name'=>'Pakistan','currency'=>'PKR','region'=>4); 
	$countries['PW'] = array('name'=>'Palau','currency'=>'USD','region'=>7); 
	$countries['PA'] = array('name'=>'Panama','currency'=>'PAB','region'=>1); 
	$countries['PG'] = array('name'=>'Papua New Guinea','currency'=>'PGK','region'=>7); 
	$countries['PY'] = array('name'=>'Paraguay','currency'=>'PYG','region'=>2); 
	$countries['PE'] = array('name'=>'Peru','currency'=>'PEN','region'=>2); 
	$countries['PH'] = array('name'=>'Philippines','currency'=>'PHP','region'=>6); 
	$countries['PN'] = array('name'=>'Pitcairn Island','currency'=>'NZD','region'=>7); 
	$countries['PL'] = array('name'=>'Poland','currency'=>'PLZ','region'=>3); 
	$countries['PF'] = array('name'=>'Polynesia (French)','currency'=>'XPF','region'=>7); 
	$countries['PT'] = array('name'=>'Portugal','currency'=>'EUR','region'=>3); 
	$countries['PR'] = array('name'=>'Puerto Rico','currency'=>'USD','region'=>0); 
	$countries['QA'] = array('name'=>'Qatar','currency'=>'QAR','region'=>4); 
	$countries['RE'] = array('name'=>'Reunion (French)','currency'=>'EUR','region'=>3); 
	$countries['RO'] = array('name'=>'Romania','currency'=>'ROL','region'=>3); 
	$countries['RU'] = array('name'=>'Russia','currency'=>'RUR','region'=>6); 
	$countries['RW'] = array('name'=>'Rwanda','currency'=>'RWF','region'=>5); 
	$countries['SH'] = array('name'=>'Saint Helena','currency'=>'SHP','region'=>5); 
	$countries['KN'] = array('name'=>'Saint Kitts &amp; Nevis Anguilla','currency'=>'XCD','region'=>0); 
	$countries['LC'] = array('name'=>'Saint Lucia','currency'=>'XCD','region'=>0); 
	$countries['PM'] = array('name'=>'Saint Pierre and Miquelon','currency'=>'EUR','region'=>3); 
	$countries['VC'] = array('name'=>'Saint Vincent &amp; Grenadines','currency'=>'XCD','region'=>0); 
	$countries['WS'] = array('name'=>'Samoa','currency'=>'WST','region'=>7); 
	$countries['SM'] = array('name'=>'San Marino','currency'=>'ITL','region'=>3); 
	$countries['ST'] = array('name'=>'Sao Tome and Principe','currency'=>'STD','region'=>5); 
	$countries['SA'] = array('name'=>'Saudi Arabia','currency'=>'SAR','region'=>4); 
	$countries['SN'] = array('name'=>'Senegal','currency'=>'XOF','region'=>5); 
	$countries['RS'] = array('name'=>'Serbia','currency'=>'RSD','region'=>3); 
	$countries['SC'] = array('name'=>'Seychelles','currency'=>'SCR','region'=>5); 
	$countries['SL'] = array('name'=>'Sierra Leone','currency'=>'SLL','region'=>5); 
	$countries['SG'] = array('name'=>'Singapore','currency'=>'SGD','region'=>6); 
	$countries['SK'] = array('name'=>'Slovakia','currency'=>'SKK','region'=>3); 
	$countries['SI'] = array('name'=>'Slovenia','currency'=>'SIT','region'=>3); 
	$countries['SB'] = array('name'=>'Solomon Islands','currency'=>'SBD','region'=>7); 
	$countries['SO'] = array('name'=>'Somalia','currency'=>'SOD','region'=>5); 
	$countries['ZA'] = array('name'=>'South Africa','currency'=>'ZAR','region'=>5); 
	$countries['GS'] = array('name'=>'South Georgia &amp; South Sandwich Islands','currency'=>'GBP','region'=>2); 
	$countries['ES'] = array('name'=>'Spain','currency'=>'EUR','region'=>3); 
	$countries['LK'] = array('name'=>'Sri Lanka','currency'=>'LKR','region'=>6); 
	$countries['SD'] = array('name'=>'Sudan','currency'=>'SDD','region'=>5); 
	$countries['SR'] = array('name'=>'Suriname','currency'=>'SRG','region'=>2); 
	$countries['SJ'] = array('name'=>'Svalbard and Jan Mayen Islands','currency'=>'NOK','region'=>3); 
	$countries['SZ'] = array('name'=>'Swaziland','currency'=>'SZL','region'=>5); 
	$countries['SE'] = array('name'=>'Sweden','currency'=>'SEK','region'=>3); 
	$countries['CH'] = array('name'=>'Switzerland','currency'=>'CHF','region'=>3); 
	$countries['SY'] = array('name'=>'Syria','currency'=>'SYP','region'=>4); 
	$countries['TW'] = array('name'=>'Taiwan','currency'=>'TWD','region'=>6); 
	$countries['TJ'] = array('name'=>'Tajikistan','currency'=>'TJR','region'=>6); 
	$countries['TZ'] = array('name'=>'Tanzania','currency'=>'TZS','region'=>5); 
	$countries['TH'] = array('name'=>'Thailand','currency'=>'THB','region'=>6); 
	$countries['TG'] = array('name'=>'Togo','currency'=>'XOF','region'=>5); 
	$countries['TK'] = array('name'=>'Tokelau','currency'=>'NZD','region'=>7); 
	$countries['TO'] = array('name'=>'Tonga','currency'=>'TOP','region'=>7); 
	$countries['TT'] = array('name'=>'Trinidad and Tobago','currency'=>'TTD','region'=>0); 
	$countries['TN'] = array('name'=>'Tunisia','currency'=>'TND','region'=>5); 
	$countries['TR'] = array('name'=>'Turkey','currency'=>'TRL','region'=>4); 
	$countries['TM'] = array('name'=>'Turkmenistan','currency'=>'TMM','region'=>6); 
	$countries['TC'] = array('name'=>'Turks and Caicos Islands','currency'=>'USD','region'=>0); 
	$countries['TV'] = array('name'=>'Tuvalu','currency'=>'AUD','region'=>7); 
	$countries['UG'] = array('name'=>'Uganda','currency'=>'UGS','region'=>5); 
	$countries['UA'] = array('name'=>'Ukraine','currency'=>'UAG','region'=>3); 
	$countries['AE'] = array('name'=>'United Arab Emirates','currency'=>'AED','region'=>4); 
	$countries['UY'] = array('name'=>'Uruguay','currency'=>'UYP','region'=>2); 
	$countries['US'] = array('name'=>'USA','currency'=>'USD','region'=>0); 
	$countries['UM'] = array('name'=>'USA Minor Outlying Islands','currency'=>'USD','region'=>0); 
	$countries['UZ'] = array('name'=>'Uzbekistan','currency'=>'UZS','region'=>6); 
	$countries['VU'] = array('name'=>'Vanuatu','currency'=>'VUV','region'=>7); 
	$countries['VA'] = array('name'=>'Vatican','currency'=>'EUR','region'=>3); 
	$countries['VE'] = array('name'=>'Venezuela','currency'=>'VUB','region'=>2); 
	$countries['VN'] = array('name'=>'Vietnam','currency'=>'VND','region'=>6); 
	$countries['VG'] = array('name'=>'Virgin Islands (British)','currency'=>'USD','region'=>0); 
	$countries['VI'] = array('name'=>'Virgin Islands (USA)','currency'=>'USD','region'=>0); 
	$countries['WF'] = array('name'=>'Wallis and Futuna Islands','currency'=>'XPF','region'=>7); 
	$countries['EH'] = array('name'=>'Western Sahara','currency'=>'MAD','region'=>5); 
	$countries['YE'] = array('name'=>'Yemen','currency'=>'YER','region'=>4); 
	$countries['YU'] = array('name'=>'Yugoslavia','currency'=>'YUN','region'=>3); 
	$countries['ZM'] = array('name'=>'Zambia','currency'=>'ZMK','region'=>5); 
	$countries['ZW'] = array('name'=>'Zimbabwe','currency'=>'ZWD','region'=>5);
	return $countries;
}

/**
 * State/Province/Territory zone names
 * 2 KB in the database */
function get_country_zones() {
	$zones = array();
	$zones['AU'] = array();
	$zones['AU']['NSW'] = 'New South Wales';
	$zones['AU']['NT'] = 'Northern Territory';
	$zones['AU']['QLD'] = 'Queensland';
	$zones['AU']['TAS'] = 'Tasmania';
	$zones['AU']['VIC'] = 'Victoria';
	$zones['AU']['WA'] = 'Western Australia';

	$zones['CA'] = array();
	$zones['CA']['AB'] = 'Alberta';
	$zones['CA']['BC'] = 'British Columbia';
	$zones['CA']['MB'] = 'Manitoba';
	$zones['CA']['NB'] = 'New Brunswick';
	$zones['CA']['NF'] = 'Newfoundland';
	$zones['CA']['NT'] = 'Northwest Territories';
	$zones['CA']['NS'] = 'Nova Scotia';
	$zones['CA']['NU'] = 'Nunavut';
	$zones['CA']['ON'] = 'Ontario';
	$zones['CA']['PE'] = 'Prince Edward Island';
	$zones['CA']['PQ'] = 'Quebec';
	$zones['CA']['SK'] = 'Saskatchewan';
	$zones['CA']['YT'] = 'Yukon Territory';

	$zones['US'] = array();
	$zones['US']['AL'] = 'Alabama';
	$zones['US']['AK'] = 'Alaska ';
	$zones['US']['AZ'] = 'Arizona';
	$zones['US']['AR'] = 'Arkansas';
	$zones['US']['CA'] = 'California ';
	$zones['US']['CO'] = 'Colorado';
	$zones['US']['CT'] = 'Connecticut';
	$zones['US']['DE'] = 'Delaware';
	$zones['US']['DC'] = 'District Of Columbia ';
	$zones['US']['FL'] = 'Florida';
	$zones['US']['GA'] = 'Georgia ';
	$zones['US']['HI'] = 'Hawaii';
	$zones['US']['ID'] = 'Idaho';
	$zones['US']['IL'] = 'Illinois';
	$zones['US']['IN'] = 'Indiana';
	$zones['US']['IA'] = 'Iowa';
	$zones['US']['KS'] = 'Kansas';
	$zones['US']['KY'] = 'Kentucky';
	$zones['US']['LA'] = 'Louisiana';
	$zones['US']['ME'] = 'Maine';
	$zones['US']['MD'] = 'Maryland';
	$zones['US']['MA'] = 'Massachusetts';
	$zones['US']['MI'] = 'Michigan';
	$zones['US']['MN'] = 'Minnesota';
	$zones['US']['MS'] = 'Mississippi';
	$zones['US']['MO'] = 'Missouri';
	$zones['US']['MT'] = 'Montana';
	$zones['US']['NE'] = 'Nebraska';
	$zones['US']['NV'] = 'Nevada';
	$zones['US']['NH'] = 'New Hampshire';
	$zones['US']['NJ'] = 'New Jersey';
	$zones['US']['NM'] = 'New Mexico';
	$zones['US']['NY'] = 'New York';
	$zones['US']['NC'] = 'North Carolina';
	$zones['US']['ND'] = 'North Dakota';
	$zones['US']['OH'] = 'Ohio';
	$zones['US']['OK'] = 'Oklahoma';
	$zones['US']['OR'] = 'Oregon';
	$zones['US']['PA'] = 'Pennsylvania';
	$zones['US']['RI'] = 'Rhode Island';
	$zones['US']['SC'] = 'South Carolina';
	$zones['US']['SD'] = 'South Dakota';
	$zones['US']['TN'] = 'Tennessee';
	$zones['US']['TX'] = 'Texas';
	$zones['US']['UT'] = 'Utah';
	$zones['US']['VT'] = 'Vermont';
	$zones['US']['VA'] = 'Virginia';
	$zones['US']['WA'] = 'Washington';
	$zones['US']['WV'] = 'West Virginia';
	$zones['US']['WI'] = 'Wisconsin';
	$zones['US']['WY'] = 'Wyoming';
	return $zones;
}

/**
 * Domestic areas for US and Canada mapped by postcode
 * 3 KB in the database */
function get_country_areas () {
	$areas = array();
	$areas['CA'] = array();
	$areas['CA']['Northern Canada'] = array('YT'=>array('Y'),'NT'=>array('X'),'NU'=>array('X'));
	$areas['CA']['Western Canada'] = array('BC'=>array('V'),'AB'=>array('T'),'SK'=>array('S'),'MB'=>array('R'));
	$areas['CA']['Eastern Canada'] = array('OT'=>array('K','L','M','N','P'),'PQ'=>array('G','H','J'),'NB'=>array('E'),'PE'=>array('C'),'NS'=>array('B'),'NF'=>array('A'));

	$areas['US'] = array();
	$areas['US']['Northeast US'] = array('MA'=>array('01000','02799'),'RI'=>array('02800','02999'),'NH'=>array('03000','03899'),'ME'=>array('04000','04999'),'VT'=>array('05000','05999'),'CT'=>array('06800','06999'),'NJ'=>array('07000','08999'),'NY'=>array('10000','14999'),'PA'=>array('15000','16999'));
	$areas['US']['Midwest US'] = array('OH'=>array('43000','45899'),'IN'=>array('46000','47999'),'MI'=>array('48000','49799'),'IA'=>array('50000','52899'),'WI'=>array('53000','54899'),'MN'=>array('55000','56799'),'SD'=>array('57000','57799'),'ND'=>array('58000','58899'),'IL'=>array('60000','62999'),'MO'=>array('63000','65899'),'KS'=>array('66000','64799'),'NE'=>array('68000','69399'));
	$areas['US']['South US'] =array('DE'=>array('19700','19999'),'DC'=>array('20001','20599'),'MD'=>array('20600','21999'),'VA'=>array('22000','24699'),'WV'=>array('25000','26899'),'NC'=>array('27000','28999'),'SC'=>array('29000','29999'),'GA'=>array('30000','31999'),'FL'=>array('32100','34999'),'AL'=>array('35000','36999'),'TN'=>array('37000','38899'),'MS'=>array('38600','39599'),'KY'=>array('40000','42799'),'LA'=>array('70000','71499'),'AR'=>array('71600','72999'),'OK'=>array('73000','74999'),'TX'=>array('75000','79999'));
	$areas['US']['West US'] =array('MT'=>array('59000','59999'),'CO'=>array('80000','81699'),'WY'=>array('82000','83199'),'ID'=>array('83200','83899'),'UT'=>array('84000','84799'),'AZ'=>array('85000','86599'),'NM'=>array('87000','88499'),'NV'=>array('89000','89899'),'CA'=>array('90000','96199'),'HI'=>array('96700','96899'),'OR'=>array('97000','97999'),'WA'=>array('98000','99499'),'AK'=>array('99500','99999'));
	return $areas;	
}

/**
 * Currency refernece table
 * 16 KB in the database, load only when absolutely necessary and unset() asap */
function get_currencies () {
	// Currency data set
	$currencies = array();
	$currencies['AED'] = array('name'=>'Arab Emirates Dirham','symbol'=>'','entity'=>''); 
	$currencies['AFA'] = array('name'=>'Afghanistan Afghani','symbol'=>'','entity'=>''); 
	$currencies['ALL'] = array('name'=>'Albanian Lek','symbol'=>'','entity'=>''); 
	$currencies['AMD'] = array('name'=>'Armenian Dram','symbol'=>'','entity'=>''); 
	$currencies['ANG'] = array('name'=>'Netherlands Antillean Guilder','symbol'=>'','entity'=>''); 
	$currencies['AON'] = array('name'=>'Angolan New Kwanza','symbol'=>'','entity'=>''); 
	$currencies['ARS'] = array('name'=>'Argentine Peso','symbol'=>'','entity'=>''); 
	$currencies['ATA'] = array('name'=>'Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['AUD'] = array('name'=>'Australian Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['AWG'] = array('name'=>'Aruban Guilder','symbol'=>'','entity'=>''); 
	$currencies['AZM'] = array('name'=>'Azerbaijanian Manat','symbol'=>'','entity'=>''); 
	$currencies['BAM'] = array('name'=>'Marka','symbol'=>'','entity'=>''); 
	$currencies['BBD'] = array('name'=>'Barbados Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['BDT'] = array('name'=>'Bangladeshi Taka','symbol'=>'','entity'=>''); 
	$currencies['BGL'] = array('name'=>'Bulgarian Lev','symbol'=>'','entity'=>''); 
	$currencies['BHD'] = array('name'=>'Bahraini Dinar','symbol'=>'','entity'=>''); 
	$currencies['BIF'] = array('name'=>'Burundi Franc','symbol'=>'','entity'=>''); 
	$currencies['BMD'] = array('name'=>'Bermudian Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['BND'] = array('name'=>'Brunei Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['BOB'] = array('name'=>'Boliviano','symbol'=>'','entity'=>''); 
	$currencies['BRL'] = array('name'=>'Brazilian Real','symbol'=>'','entity'=>''); 
	$currencies['BSD'] = array('name'=>'Bahamian Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['BTN'] = array('name'=>'Bhutan Ngultrum','symbol'=>'','entity'=>''); 
	$currencies['BWP'] = array('name'=>'Botswana Pula','symbol'=>'','entity'=>''); 
	$currencies['BYB'] = array('name'=>'Belarussian Ruble','symbol'=>'','entity'=>''); 
	$currencies['BZD'] = array('name'=>'Belize Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['CAD'] = array('name'=>'Canadian Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['CDF'] = array('name'=>'Francs','symbol'=>'','entity'=>''); 
	$currencies['CHF'] = array('name'=>'Swiss Franc','symbol'=>'','entity'=>''); 
	$currencies['CLP'] = array('name'=>'Chilean Peso','symbol'=>'','entity'=>''); 
	$currencies['CNY'] = array('name'=>'Yuan Renminbi','symbol'=>'','entity'=>''); 
	$currencies['COP'] = array('name'=>'Colombian Peso','symbol'=>'','entity'=>''); 
	$currencies['CRC'] = array('name'=>'Costa Rican Colon','symbol'=>'','entity'=>''); 
	$currencies['CUP'] = array('name'=>'Cuban Peso','symbol'=>'','entity'=>''); 
	$currencies['CVE'] = array('name'=>'Cape Verde Escudo','symbol'=>'','entity'=>''); 
	$currencies['CYP'] = array('name'=>'Cyprus Pound','symbol'=>'','entity'=>''); 
	$currencies['CZK'] = array('name'=>'Czech Koruna','symbol'=>'','entity'=>''); 
	$currencies['DJF'] = array('name'=>'Djibouti Franc','symbol'=>'','entity'=>''); 
	$currencies['DKK'] = array('name'=>'Danish Krone','symbol'=>'','entity'=>''); 
	$currencies['DOP'] = array('name'=>'Dominican Peso','symbol'=>'','entity'=>''); 
	$currencies['DZD'] = array('name'=>'Algerian Dinar','symbol'=>'','entity'=>''); 
	$currencies['ECS'] = array('name'=>'Ecuador Sucre','symbol'=>'','entity'=>''); 
	$currencies['EEK'] = array('name'=>'Estonian Kroon','symbol'=>'','entity'=>''); 
	$currencies['EGP'] = array('name'=>'Egyptian Pound','symbol'=>'','entity'=>''); 
	$currencies['ERN'] = array('name'=>'Eritrean Nakfa','symbol'=>'','entity'=>''); 
	$currencies['ETB'] = array('name'=>'Ethiopian Birr','symbol'=>'','entity'=>''); 
	$currencies['EUR'] = array('name'=>'Euro','symbol'=>'€','entity'=>'&#8364;'); 
	$currencies['FJD'] = array('name'=>'Fiji Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['FKP'] = array('name'=>'Falkland Islands Pound','symbol'=>'','entity'=>''); 
	$currencies['GBP'] = array('name'=>'Pound Sterling','symbol'=>'£','entity'=>'&#163;'); 
	$currencies['GEL'] = array('name'=>'Georgian Lari','symbol'=>'','entity'=>''); 
	$currencies['GHC'] = array('name'=>'Ghanaian Cedi','symbol'=>'','entity'=>''); 
	$currencies['GIP'] = array('name'=>'Gibraltar Pound','symbol'=>'','entity'=>''); 
	$currencies['GMD'] = array('name'=>'Gambian Dalasi','symbol'=>'','entity'=>''); 
	$currencies['GNF'] = array('name'=>'Guinea Franc','symbol'=>'','entity'=>''); 
	$currencies['GWP'] = array('name'=>'Guinea-Bissau Peso','symbol'=>'','entity'=>''); 
	$currencies['GYD'] = array('name'=>'Guyana Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['HKD'] = array('name'=>'Hong Kong Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['HNL'] = array('name'=>'Honduran Lempira','symbol'=>'','entity'=>''); 
	$currencies['HRK'] = array('name'=>'Croatian Kuna','symbol'=>'','entity'=>''); 
	$currencies['HTG'] = array('name'=>'Haitian Gourde','symbol'=>'','entity'=>''); 
	$currencies['HUF'] = array('name'=>'Hungarian Forint','symbol'=>'','entity'=>''); 
	$currencies['IDR'] = array('name'=>'Indonesian Rupiah','symbol'=>'','entity'=>''); 
	$currencies['ILS'] = array('name'=>'Israeli New Shekel','symbol'=>'','entity'=>''); 
	$currencies['INR'] = array('name'=>'Indian Rupee','symbol'=>'','entity'=>''); 
	$currencies['IQD'] = array('name'=>'Iraqi Dinar','symbol'=>'','entity'=>''); 
	$currencies['IRR'] = array('name'=>'Iranian Rial','symbol'=>'','entity'=>''); 
	$currencies['ISK'] = array('name'=>'Iceland Krona','symbol'=>'','entity'=>''); 
	$currencies['ITL'] = array('name'=>'Italian Lira','symbol'=>'','entity'=>''); 
	$currencies['JMD'] = array('name'=>'Jamaican Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['JOD'] = array('name'=>'Jordanian Dinar','symbol'=>'','entity'=>''); 
	$currencies['JPY'] = array('name'=>'Japanese Yen','symbol'=>'¥','entity'=>'&#165;'); 
	$currencies['KES'] = array('name'=>'Kenyan Shilling','symbol'=>'','entity'=>''); 
	$currencies['KGS'] = array('name'=>'Som','symbol'=>'','entity'=>''); 
	$currencies['KHR'] = array('name'=>'Kampuchean Riel','symbol'=>'','entity'=>''); 
	$currencies['KMF'] = array('name'=>'Comoros Franc','symbol'=>'','entity'=>''); 
	$currencies['KPW'] = array('name'=>'North Korean Won','symbol'=>'','entity'=>''); 
	$currencies['KRW'] = array('name'=>'Korean Won','symbol'=>'','entity'=>''); 
	$currencies['KWD'] = array('name'=>'Kuwaiti Dinar','symbol'=>'','entity'=>''); 
	$currencies['KYD'] = array('name'=>'Cayman Islands Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['KZT'] = array('name'=>'Kazakhstan Tenge','symbol'=>'','entity'=>''); 
	$currencies['LAK'] = array('name'=>'Lao Kip','symbol'=>'','entity'=>''); 
	$currencies['LBP'] = array('name'=>'Lebanese Pound','symbol'=>'','entity'=>''); 
	$currencies['LKR'] = array('name'=>'Sri Lanka Rupee','symbol'=>'','entity'=>''); 
	$currencies['LRD'] = array('name'=>'Liberian Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['LSL'] = array('name'=>'Lesotho Loti','symbol'=>'','entity'=>''); 
	$currencies['LTL'] = array('name'=>'Lithuanian Litas','symbol'=>'','entity'=>''); 
	$currencies['LVL'] = array('name'=>'Latvian Lats','symbol'=>'','entity'=>''); 
	$currencies['LYD'] = array('name'=>'Libyan Dinar','symbol'=>'','entity'=>''); 
	$currencies['MAD'] = array('name'=>'Moroccan Dirham','symbol'=>'','entity'=>''); 
	$currencies['MDL'] = array('name'=>'Moldovan Leu','symbol'=>'','entity'=>''); 
	$currencies['MGF'] = array('name'=>'Malagasy Franc','symbol'=>'','entity'=>''); 
	$currencies['MKD'] = array('name'=>'Denar','symbol'=>'','entity'=>''); 
	$currencies['MMK'] = array('name'=>'Myanmar Kyat','symbol'=>'','entity'=>''); 
	$currencies['MNT'] = array('name'=>'Mongolian Tugrik','symbol'=>'','entity'=>''); 
	$currencies['MOP'] = array('name'=>'Macau Pataca','symbol'=>'','entity'=>''); 
	$currencies['MRO'] = array('name'=>'Mauritanian Ouguiya','symbol'=>'','entity'=>''); 
	$currencies['MTL'] = array('name'=>'Maltese Lira','symbol'=>'','entity'=>''); 
	$currencies['MUR'] = array('name'=>'Mauritius Rupee','symbol'=>'','entity'=>''); 
	$currencies['MVR'] = array('name'=>'Maldive Rufiyaa','symbol'=>'','entity'=>''); 
	$currencies['MWK'] = array('name'=>'Malawi Kwacha','symbol'=>'','entity'=>''); 
	$currencies['MXN'] = array('name'=>'Mexican Nuevo Peso','symbol'=>'','entity'=>''); 
	$currencies['MYR'] = array('name'=>'Malaysian Ringgit','symbol'=>'','entity'=>''); 
	$currencies['MZM'] = array('name'=>'Mozambique Metical','symbol'=>'','entity'=>''); 
	$currencies['NAD'] = array('name'=>'Namibian Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['NGN'] = array('name'=>'Nigerian Naira','symbol'=>'','entity'=>''); 
	$currencies['NIC'] = array('name'=>'Nicaraguan Cordoba Oro','symbol'=>'','entity'=>''); 
	$currencies['NOK'] = array('name'=>'Norwegian Krone','symbol'=>'','entity'=>''); 
	$currencies['NPR'] = array('name'=>'Nepalese Rupee','symbol'=>'','entity'=>''); 
	$currencies['NZD'] = array('name'=>'New Zealand Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['OMR'] = array('name'=>'Omani Rial','symbol'=>'','entity'=>''); 
	$currencies['PAB'] = array('name'=>'Panamanian Balboa','symbol'=>'','entity'=>''); 
	$currencies['PEN'] = array('name'=>'Peruvian Nuevo Sol','symbol'=>'','entity'=>''); 
	$currencies['PGK'] = array('name'=>'Papua New Guinea Kina','symbol'=>'','entity'=>''); 
	$currencies['PHP'] = array('name'=>'Philippine Peso','symbol'=>'','entity'=>''); 
	$currencies['PKR'] = array('name'=>'Pakistan Rupee','symbol'=>'','entity'=>''); 
	$currencies['PLZ'] = array('name'=>'Polish Zloty','symbol'=>'','entity'=>''); 
	$currencies['PYG'] = array('name'=>'Paraguay Guarani','symbol'=>'','entity'=>''); 
	$currencies['QAR'] = array('name'=>'Qatari Rial','symbol'=>'','entity'=>''); 
	$currencies['QTQ'] = array('name'=>'Guatemalan Quetzal','symbol'=>'','entity'=>''); 
	$currencies['ROL'] = array('name'=>'Romanian Leu','symbol'=>'','entity'=>''); 
	$currencies['RSD'] = array('name'=>'Serbian Dinar','symbol'=>'','entity'=>''); 
	$currencies['RUR'] = array('name'=>'Russian Ruble','symbol'=>'','entity'=>''); 
	$currencies['RWF'] = array('name'=>'Rwanda Franc','symbol'=>'','entity'=>''); 
	$currencies['SAR'] = array('name'=>'Saudi Riyal','symbol'=>'','entity'=>''); 
	$currencies['SBD'] = array('name'=>'Solomon Islands Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['SCR'] = array('name'=>'Seychelles Rupee','symbol'=>'','entity'=>''); 
	$currencies['SDD'] = array('name'=>'Sudanese Dinar','symbol'=>'','entity'=>''); 
	$currencies['SEK'] = array('name'=>'Swedish Krona','symbol'=>'','entity'=>''); 
	$currencies['SGD'] = array('name'=>'Singapore Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['SHP'] = array('name'=>'St. Helena Pound','symbol'=>'','entity'=>''); 
	$currencies['SIT'] = array('name'=>'Slovenian Tolar','symbol'=>'','entity'=>''); 
	$currencies['SKK'] = array('name'=>'Slovak Koruna','symbol'=>'','entity'=>''); 
	$currencies['SLL'] = array('name'=>'Sierra Leone Leone','symbol'=>'','entity'=>''); 
	$currencies['SOD'] = array('name'=>'Somali Shilling','symbol'=>'','entity'=>''); 
	$currencies['SRG'] = array('name'=>'Surinam Guilder','symbol'=>'','entity'=>''); 
	$currencies['STD'] = array('name'=>'Dobra','symbol'=>'','entity'=>''); 
	$currencies['SVC'] = array('name'=>'El Salvador Colon','symbol'=>'','entity'=>''); 
	$currencies['SYP'] = array('name'=>'Syrian Pound','symbol'=>'','entity'=>''); 
	$currencies['SZL'] = array('name'=>'Swaziland Lilangeni','symbol'=>'','entity'=>''); 
	$currencies['THB'] = array('name'=>'Thai Baht','symbol'=>'','entity'=>''); 
	$currencies['TJR'] = array('name'=>'Tajik Ruble','symbol'=>'','entity'=>''); 
	$currencies['TMM'] = array('name'=>'Manat','symbol'=>'','entity'=>''); 
	$currencies['TND'] = array('name'=>'Tunisian Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['TOP'] = array('name'=>'Tongan Pa\'anga','symbol'=>'','entity'=>''); 
	$currencies['TPE'] = array('name'=>'Timor Escudo','symbol'=>'','entity'=>''); 
	$currencies['TRL'] = array('name'=>'Turkish Lira','symbol'=>'','entity'=>''); 
	$currencies['TTD'] = array('name'=>'Trinidad and Tobago Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['TWD'] = array('name'=>'Taiwan Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['TZS'] = array('name'=>'Tanzanian Shilling','symbol'=>'','entity'=>''); 
	$currencies['UAG'] = array('name'=>'Ukraine Hryvnia','symbol'=>'','entity'=>''); 
	$currencies['UGS'] = array('name'=>'Uganda Shilling','symbol'=>'','entity'=>''); 
	$currencies['USD'] = array('name'=>'US Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['UYP'] = array('name'=>'Uruguayan Peso','symbol'=>'','entity'=>''); 
	$currencies['UZS'] = array('name'=>'Uzbekistan Sum','symbol'=>'','entity'=>''); 
	$currencies['VND'] = array('name'=>'Vietnamese Dong','symbol'=>'','entity'=>''); 
	$currencies['VUB'] = array('name'=>'Venezuelan Bolivar','symbol'=>'','entity'=>''); 
	$currencies['VUV'] = array('name'=>'Vanuatu Vatu','symbol'=>'','entity'=>''); 
	$currencies['WST'] = array('name'=>'Samoan Tala','symbol'=>'','entity'=>''); 
	$currencies['XAF'] = array('name'=>'CFA Franc BEAC','symbol'=>'','entity'=>''); 
	$currencies['XCD'] = array('name'=>'East Carribean Dollar','symbol'=>'$','entity'=>'&#036;'); 
	$currencies['XOF'] = array('name'=>'CFA Franc BCEAO','symbol'=>'','entity'=>''); 
	$currencies['XPF'] = array('name'=>'CFP Franc','symbol'=>'','entity'=>''); 
	$currencies['YER'] = array('name'=>'Yemeni Rial','symbol'=>'','entity'=>''); 
	$currencies['YUN'] = array('name'=>'Yugoslav New Dinar','symbol'=>'','entity'=>''); 
	$currencies['ZAR'] = array('name'=>'South African Rand','symbol'=>'','entity'=>''); 
	$currencies['ZMK'] = array('name'=>'Zambian Kwacha','symbol'=>'','entity'=>''); 
	$currencies['ZWD'] = array('name'=>'Zimbabwe Dollar','symbol'=>'','entity'=>'&#036;');
	return $currencies;
}

?>