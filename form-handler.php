<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input
    $name = htmlspecialchars(trim($_POST['company']));
    $contact_name = htmlspecialchars(trim($_POST['contact_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $machine_brand = htmlspecialchars(trim($_POST['machine_brand'] ?? ''));
    $service_type = htmlspecialchars(trim($_POST['service_type']));
    $description = htmlspecialchars(trim($_POST['description']));
    $urgency = htmlspecialchars(trim($_POST['urgency'] ?? 'Standard'));
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address");
    }
    
    // Create the email content
    $subject = "New Spindle Repair Inquiry from " . $name;
    
    $message = "
    <html>
    <head>
        <title>New Spindle Repair Inquiry</title>
    </head>
    <body style='font-family: Arial, sans-serif;'>
        <h2 style='color: #0066cc;'>New Spindle Repair Inquiry</h2>
        
        <h3>Customer Information:</h3>
        <p><strong>Company:</strong> " . $name . "</p>
        <p><strong>Contact Name:</strong> " . $contact_name . "</p>
        <p><strong>Email:</strong> " . $email . "</p>
        <p><strong>Phone:</strong> " . $phone . "</p>
        
        <h3>Service Details:</h3>
        <p><strong>Machine/Spindle Brand:</strong> " . ($machine_brand ?: 'Not provided') . "</p>
        <p><strong>Service Type:</strong> " . $service_type . "</p>
        <p><strong>Urgency Level:</strong> " . $urgency . "</p>
        
        <h3>Issue Description:</h3>
        <p>" . nl2br($description) . "</p>
        
        <hr>
        <p style='color: #666; font-size: 12px;'>This inquiry was submitted on " . date('Y-m-d H:i:s') . "</p>
    </body>
    </html>
    ";
    
    // Set headers for HTML email
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . $email . "\r\n";
    
    // IMPORTANT: Replace 'YOUR_EMAIL_HERE' with your actual email address
    $to = 'YOUR_EMAIL_HERE';
    
    // Send the email
    if (mail($to, $subject, $message, $headers)) {
        // Optional: Send confirmation email to customer
        $customer_subject = "We Received Your Spindle Repair Inquiry - Aldridge Industries";
        $customer_message = "
        <html>
        <head>
            <title>Inquiry Received</title>
        </head>
        <body style='font-family: Arial, sans-serif;'>
            <h2 style='color: #0066cc;'>Thank You!</h2>
            <p>Dear " . $contact_name . ",</p>
            <p>We have received your spindle repair inquiry and appreciate your interest in Aldridge Industries.</p>
            <p>Our team will review your request and contact you within 24 hours with a customized quote and next steps.</p>
            <p style='margin-top: 2rem;'>Best regards,<br><strong>Aldridge Industries Team</strong></p>
        </body>
        </html>
        ";
        
        $customer_headers = "MIME-Version: 1.0\r\n";
        $customer_headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $customer_headers .= "From: noreply@aldridgeindustries.com\r\n";
        
        mail($email, $customer_subject, $customer_message, $customer_headers);
        
        // Redirect to success page or display message
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Your inquiry has been submitted successfully!']);
        exit();
    } else {
        header('Content-Type: application/json', true, 400);
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Please try again later.']);
        exit();
    }
} else {
    // If not a POST request, redirect back to the form
    header('Location: index.html');
    exit();
}
?>