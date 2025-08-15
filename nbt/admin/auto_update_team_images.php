<?php
require '../config/db.php';

// Define the team folder path
$team_folder = '../uploads/team/';

// Mapping between team member names and their image files
$team_image_mapping = [
    'Prathmesh Gajbhiye' => 'prathmesh_gajbhiye_1.jpg',
    'MD Ejaz Ansari' => 'md_ejaz_ansari_2.jpg',
    'Neha Saha' => 'neha_saha_3.jpg', // There are two Neha Saha entries, using first one
    'NIHARIKA SHAW' => 'niharika_shaw_4.jpg',
    'Ajeet Kumar' => 'ajeet_kumar_5.jpg',
    'Arjun Barman' => 'arjun_barman_6.jpg',
    'Prakash Kumar Shah' => 'prakash_kumar_shah_7.jpg',
    'Sahil Prasad' => 'sahil_prasad_8.jpg', // There are two Sahil Prasad entries, using first one
    'Ratan Randhir' => 'ratan_randhir_9.jpg',
    'Rishabh Singh Negi' => 'rishabh_singh_negi_10.jpg',
    'Chandan Gautam' => 'chandan_gautam_12.jpg',
    'Sumit Kami' => 'sumit_kami_14.jpg',
    'PRANJAL RAWAT' => 'pranjal_rawat_15.jpg',
    'PRIYANSHU RAJ SINGH' => 'priyanshu_raj_singh_16.jpg',
    'Shikat Sarkar' => 'shikat_sarkar_17.jpg',
    'RAJ KUMAR PRASAD' => 'raj_kumar_prasad_18.jpg',
    'Nidhi Singh' => 'nidhi_singh_19.jpg',
    'Amit Gautam' => 'amit_gautam_20.jpg',
    'Sanjeev Kumar Singh' => 'sanjeev_kumar_singh_21.jpg',
    'ROSHAN SRIVASTAV' => 'roshan_srivastav_22.jpg',
    'Manasvi Giri' => 'manasvi_giri_23.jpg',
    'Dorjee Namgyal Lepcha' => 'dorjee_namgyal_lepcha_24.jpg',
    'Sangay Chopel Bhutia' => 'sangay_chopel_bhutia_25.jpg',
    'KHUSHI KUMARI' => 'khushi_kumari_26.jpg'
];

// Handle duplicate names
$duplicate_mappings = [
    'Neha Saha' => ['neha_saha_3.jpg', 'neha_saha_11.jpg'],
    'Sahil Prasad' => ['sahil_prasad_8.jpg', 'sahil_prasad_13.jpg']
];

function getImageMimeType($file_path) {
    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            return 'image/jpeg';
        case 'png':
            return 'image/png';
        case 'gif':
            return 'image/gif';
        case 'webp':
            return 'image/webp';
        default:
            return 'image/jpeg';
    }
}

// Get all team members from database
$stmt = $pdo->query("SELECT id, name FROM meet_our_team ORDER BY image_sequence");
$team_members = $stmt->fetchAll();

$updated_count = 0;
$failed_count = 0;
$duplicate_counters = [];

echo "Starting local image path update process...\n\n";

foreach ($team_members as $member) {
    $member_name = trim($member['name']);
    $image_file = null;
    
    // Handle duplicate names
    if (isset($duplicate_mappings[$member_name])) {
        if (!isset($duplicate_counters[$member_name])) {
            $duplicate_counters[$member_name] = 0;
        }
        $image_file = $duplicate_mappings[$member_name][$duplicate_counters[$member_name]];
        $duplicate_counters[$member_name]++;
    } elseif (isset($team_image_mapping[$member_name])) {
        $image_file = $team_image_mapping[$member_name];
    }
    
    if ($image_file) {
        $image_path = $team_folder . $image_file;
        
        echo "Processing: " . $member_name . "\n";
        echo "  - Image file: " . $image_file . "\n";
        
        if (file_exists($image_path)) {
            $image_size = filesize($image_path);
            $mime_type = getImageMimeType($image_path);
            
            // Store the relative path to the image instead of binary data
            $relative_image_path = 'uploads/team/' . $image_file;
            
            // Update database with image path instead of binary data
            $update_stmt = $pdo->prepare("UPDATE meet_our_team SET image_name = ?, image_type = ?, image_size = ?, image_path = ? WHERE id = ?");
            $result = $update_stmt->execute([$image_file, $mime_type, $image_size, $relative_image_path, $member['id']]);
            
            if ($result) {
                echo "  ✓ Successfully updated image path (" . number_format($image_size / 1024, 2) . " KB)\n";
                $updated_count++;
            } else {
                echo "  ✗ Failed to update database\n";
                $failed_count++;
            }
        } else {
            echo "  ✗ Image file not found: " . $image_path . "\n";
            $failed_count++;
        }
    } else {
        echo "No image mapping found for: " . $member_name . "\n";
        $failed_count++;
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Successfully updated: " . $updated_count . " images\n";
echo "Failed: " . $failed_count . " images\n";
echo "Total processed: " . count($team_members) . " team members\n";

if ($updated_count > 0) {
    echo "\n✅ Team image paths have been successfully updated in the database!\n";
    echo "Note: Using file-based storage instead of binary data for better performance.\n";
    echo "You may need to update the frontend to use image_path instead of image_data.\n";
}
?>
