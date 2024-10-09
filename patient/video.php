<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Consultation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        /* Sidebar styling */
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
        }

        .recordings-section {
            width: 250px;
            padding: 20px;
            background-color: #f8f9fa;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-height: 500px;
            overflow-y: auto;
            margin-right: 20px;
        }

        .recording-card {
            margin-bottom: 15px;
            background-color: #fff;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .recording-card video {
            width: 100%;
            border-radius: 5px;
        }

        .chat-container {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            max-width: 100%;
        }

        .list-group-item {
            font-size: 1.1em;
        }

        /* Video consultation styling */
        .video-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        video {
            width: 100%;
            height: auto;
            border-radius: 8px;
            background-color: #000;
        }

        .controls {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .controls button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-start {
            background-color: #28a745;
            color: white;
        }

        .btn-stop {
            background-color: #dc3545;
            color: white;
        }

        .btn-end {
            background-color: #007bff;
            color: white;
        }

        .disclaimer {
            font-size: 0.9em;
            color: #ff0000;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include '../resources/includes/p_header.php'; ?> <!-- Include the header file -->

    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?> <!-- Include the sidebar file -->

        <!-- Recordings section -->
        <div class="recordings-section">
            <h5>Your Recordings</h5>
            <div id="recordingCards">
                <!-- Recording cards will be appended here -->
            </div>
        </div>

        <div class="chat-container">
            <div class="video-container">
                <h2>Video Consultation</h2>
                <video id="video" autoplay></video>
                <div class="controls">
                    <button class="btn-start" onclick="startRecording()">Start Recording</button>
                    <button class="btn-stop" onclick="stopRecording()" disabled>Stop Recording</button>
                    <button class="btn-end" onclick="endConsultation()">End Consultation</button>
                </div>
                <div class="disclaimer">
                    <strong>Note:</strong> Ensure you have a stable internet connection during the consultation.
                </div>
            </div>
        </div>
    </div>

    <script>
        let videoStream;
        let mediaRecorder;
        let recordedChunks = [];
        let recordingIndex = 1;

        // Function to start video stream
        function startVideo() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true, audio: true })
                    .then(function (stream) {
                        videoStream = stream;
                        const videoElement = document.getElementById('video');
                        videoElement.srcObject = stream;
                        mediaRecorder = new MediaRecorder(stream);
                        mediaRecorder.ondataavailable = function (e) {
                            if (e.data.size > 0) {
                                recordedChunks.push(e.data);
                            }
                        };
                        mediaRecorder.onstop = saveRecording;
                    })
                    .catch(function (error) {
                        console.log("Error accessing camera: ", error);
                        alert("Unable to access your camera. Please check camera permissions.");
                    });
            } else {
                alert("Your browser does not support video consultation.");
            }
        }

        // Function to start recording
        function startRecording() {
            if (mediaRecorder && mediaRecorder.state !== "recording") {
                mediaRecorder.start();
                document.querySelector(".btn-stop").disabled = false;
                document.querySelector(".btn-start").disabled = true;
            }
        }

        // Function to stop recording
        function stopRecording() {
            if (mediaRecorder && mediaRecorder.state === "recording") {
                mediaRecorder.stop();
                document.querySelector(".btn-stop").disabled = true;
                document.querySelector(".btn-start").disabled = false;
            }
        }

        // Function to save recording and display it in a card
        function saveRecording() {
            const blob = new Blob(recordedChunks, { type: 'video/webm' });
            recordedChunks = [];
            const videoURL = URL.createObjectURL(blob);

            // Create and display the recording card
            const card = document.createElement('div');
            card.classList.add('recording-card');
            card.innerHTML = `
                <video controls src="${videoURL}"></video>
                <div><strong>Recording ${recordingIndex}</strong></div>
            `;
            document.getElementById('recordingCards').appendChild(card);
            recordingIndex++;
        }

        // Function to end consultation
        function endConsultation() {
            stopRecording();
            stopVideo();
            alert("Consultation has ended.");
        }

        // Function to stop video stream
        function stopVideo() {
            if (videoStream) {
                const tracks = videoStream.getTracks();
                tracks.forEach(track => track.stop());
                document.getElementById('video').srcObject = null;
            }
        }

        // Start the video stream on page load
        startVideo();
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
