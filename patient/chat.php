<?php
session_start();


if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = date('H:i'); 
}


$jsonData = file_get_contents('symptoms_data.json'); 
$symptomData = json_decode($jsonData, true);


function findBestDiagnosis($inputSymptoms, $data) {
    
    $symptomsArray = explode(' ', strtolower($inputSymptoms));
    $symptomsArray = array_map('trim', $symptomsArray); 

    $bestDiagnosis = null;
    $bestMatchCount = 0;

    foreach ($data['symptoms'] as $symptom) {
        $matchCount = 0;

        foreach ($symptomsArray as $inputSymptom) {
            
            if (strpos(strtolower($symptom['symptom']), $inputSymptom) !== false) {
                $matchCount++;
            }
        }

        
        if ($matchCount > $bestMatchCount) {
            $bestMatchCount = $matchCount;
            $bestDiagnosis = $symptom;
        }
    }

    return $bestDiagnosis;
}


if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['clear_history'])) {
        
        $_SESSION['chat_history'] = [];
        $_SESSION['start_time'] = date('H:i'); 
    } else {
        
        $userInput = trim($_POST['symptom']);
        $diagnosis = findBestDiagnosis($userInput, $symptomData);
        $timestamp = date('H:i'); 

       
        $_SESSION['chat_history'][] = [
            'user' => [
                'message' => htmlspecialchars($userInput),
                'timestamp' => $timestamp
            ],
            'bot' => $diagnosis ? [
                'diagnosis' => htmlspecialchars($diagnosis['diagnosis']),
                'prescription' => htmlspecialchars($diagnosis['prescription']),
                'timestamp' => $timestamp
            ] : null
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Symptom Checker Chatbot</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .dashboard-container {
            display: flex; 
            max-width: 100%; 
            padding: 20px;
        }

        .sidebar {
            width: 250px; 
            background-color: #fff;
            padding: 20px; 
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); 
            margin-left: 0; 
            margin-top: 0;
        }

        .chat-container {
            flex: 1; 
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            margin-left: 0; 
            max-width: 100%; 
        }

        .chat-box {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            position: relative;
        }

        .message {
            margin: 10px 0;
            padding: 12px;
            border-radius: 8px;
            max-width: fit-content;
            display: inline-block;
            position: relative;
            clear: both;
        }

        .user-message {
            text-align: right;
            background-color: #007bff;
            color: #fff;
            margin-left: 20%;
            margin-right: 0;
            float: right;
            border-top-left-radius: 0; 
        }

        .bot-message {
            text-align: left;
            background-color: #e1f5fe;
            color: #333;
            margin-right: 20%;
            margin-left: 0;
            float: left;
        }

        .form-control {
            margin-bottom: 10px;
            border-radius: 20px;
        }

        .btn-primary {
            border-radius: 20px;
        }

        .disclaimer {
            font-size: 0.9em;
            color: #ff0000;
            margin-top: 10px;
            text-align: center;
            padding: 10px;
            border: 1px solid #ff0000;
            border-radius: 8px;
            background-color: #ffe6e6;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .timestamp {
            font-size: 0.8em;
            color: #888;
            text-align: center;
            margin-top: 5px;
            display: block;
        }

        .clear-btn {
            margin-top: 10px;
            background-color: #ff4d4d;
            color: #fff;
            border-radius: 20px;
        }

        /* Icons */
        .message-icon {
            position: absolute;
            top: 12px;
            left: -40px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #007bff;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
        }

        .bot-icon {
            left: -40px;
            background-color: #e1f5fe;
        }

        .user-icon {
            left: -40px;
        }
    </style>
    <script>
        
        function scrollToBottom() {
            const chatBox = document.getElementById('chatBox');
            chatBox.scrollTop = chatBox.scrollHeight; 
        }

        
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom(); 
        });

       
        const chatBox = document.getElementById('chatBox');
        const observer = new MutationObserver(scrollToBottom);
        observer.observe(chatBox, { childList: true, subtree: true });
    </script>

</head>

<body>
    <?php include '../resources/includes/p_header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?> 

        <div class="chat-container">
            <h2 class="text-center">Online Doctor Chatbot</h2>
            <div class="chat-box" id="chatBox">
                <p><strong>Session started at:</strong> <?php echo $_SESSION['start_time']; ?></p>
                <?php
              
                foreach ($_SESSION['chat_history'] as $chat) {
                    echo '<div class="message user-message"><div class="message-icon user-icon">U</div><strong>You:</strong> ' . $chat['user']['message'] . '<br><span class="timestamp">' . $chat['user']['timestamp'] . '</span></div>';
                    if ($chat['bot']) {
                        echo '<div class="message bot-message"><div class="message-icon bot-icon">B</div><strong>Bot:</strong> Based on your input, the most likely diagnosis is:</div>';
                        echo '<div class="message bot-message"><strong>Diagnosis:</strong> ' . $chat['bot']['diagnosis'] . '<br><strong>Prescription:</strong> ' . $chat['bot']['prescription'] . '<br><span class="timestamp">' . $chat['bot']['timestamp'] . '</span></div>';
                    } else {
                        echo '<div class="message bot-message"><div class="message-icon bot-icon">B</div><strong>Bot:</strong> Sorry, I couldn\'t find any diagnoses for your symptoms.<br><span class="timestamp">' . $chat['bot']['timestamp'] . '</span></div>';
                    }
                }
                ?>
            </div>

           
            <div class="disclaimer">
                <strong>Note:</strong> This chatbot is not a substitute for professional medical advice. Always consult a healthcare provider for accurate diagnosis and treatment.
            </div>

            <form method="POST" action="">
                <input type="text" class="form-control" name="symptom" placeholder="Enter your symptoms, separated by commas or full sentences" required>
                <button type="submit" class="btn btn-primary btn-block">Check Symptoms</button>
            </form>

            
            <form method="POST" action="">
                <input type="hidden" name="clear_history" value="1">
                <button type="submit" class="btn btn-danger btn-block clear-btn">Clear Chat History</button>
            </form>
        </div>
    </div>

    <?php include '../resources/includes/footer.php'; ?>
</body>

</html>
