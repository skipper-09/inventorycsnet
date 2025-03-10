document.addEventListener("DOMContentLoaded", function () {
    // Check if this is likely a mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    // Initialize all camera components
    document.querySelectorAll('.camera-wrapper').forEach(function(wrapper) {
        const id = wrapper.dataset.id;
        
        // Set initial camera mode
        document.getElementById(`fileInput_${id}`).setAttribute("capture", "environment");
        
        if (!isMobile) {
            // For desktop: Hide mobile camera, show desktop camera
            wrapper.querySelector('.camera-mobile').style.display = 'none';
            wrapper.querySelector('.camera-desktop').style.display = 'block';
            
            // Initialize desktop camera
            // initDesktopCamera(id);
        }
    });
});

function handleCameraCapture(id) {
    const fileInput = document.getElementById(`fileInput_${id}`);
    const imagePreview = document.getElementById(`img_${id}`);
    const previewContainer = document.getElementById(`preview_${id}`);
    const hiddenInput = document.getElementById(`input_${id}`);
    
    if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];
        
        // Accept any image type
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = function (e) {
                try {
                    // Get image data
                    const imageData = e.target.result;
                    
                    // Create an image element to get dimensions
                    const img = new Image();
                    img.onload = function() {
                        // Create a canvas to ensure consistent format
                        const canvas = document.createElement('canvas');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        
                        // Draw image to canvas
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);
                        
                        // Convert to PNG format for consistency
                        const pngData = canvas.toDataURL('image/png');
                        
                        // Set image preview
                        imagePreview.src = pngData;
                        previewContainer.style.display = "block";
                        
                        // Set value to hidden input
                        hiddenInput.value = pngData;
                        
                        // console.log(`[${id}] Mobile image captured and converted to PNG`);
                    };
                    
                    img.onerror = function() {
                        console.error('Error loading image');
                        alert('Error processing image. Please try again.');
                    };
                    
                    // Load the image
                    img.src = imageData;
                    
                } catch (error) {
                    console.error('Error processing image:', error);
                    alert('Error processing image. Please try again.');
                }
            };
            
            reader.onerror = function (e) {
                console.error('Error reading file:', e);
                alert('Error reading file. Please try again.');
            };
            
            reader.readAsDataURL(file);
        } else {
            console.error('Invalid file type:', file.type);
            alert('Please upload a valid image.');
        }
    } else {
        console.error('No file selected.');
        alert('No file selected. Please capture an image.');
    }
}

function toggleCameraMode(id) {
    const fileInput = document.getElementById(`fileInput_${id}`);
    
    // Toggle attribute "capture" between "user" (front camera) and "environment" (back camera)
    if (fileInput.getAttribute("capture") === "user") {
        fileInput.setAttribute("capture", "environment");
        console.log(`[${id}] Switched to back camera`);
    } else {
        fileInput.setAttribute("capture", "user");
        console.log(`[${id}] Switched to front camera`);
    }
}

// Desktop camera functions
let cameras = {}; // Store camera instances

// function initDesktopCamera(id) {
//     cameras[id] = {
//         stream: null,
//         facingMode: 'environment', // Start with back camera
//         videoElement: document.getElementById(`video_${id}`),
//         canvasElement: document.getElementById(`canvas_${id}`)
//     };
    
//     // Start camera
//     startCamera(id);
    
//     // Setup capture button
//     document.querySelector(`.capture-btn[data-id="${id}"]`).addEventListener('click', function() {
//         captureDesktopImage(id);
//     });
    
//     // Setup switch camera button
//     document.querySelector(`.switch-cam-btn[data-id="${id}"]`).addEventListener('click', function() {
//         switchDesktopCamera(id);
//     });
// }

// function startCamera(id) {
//     // Stop any existing stream
//     if (cameras[id].stream) {
//         cameras[id].stream.getTracks().forEach(track => track.stop());
//     }
    
//     // Camera constraints
//     const constraints = {
//         video: {
//             facingMode: cameras[id].facingMode
//         },
//         audio: false
//     };
    
//     // Start video stream
//     navigator.mediaDevices.getUserMedia(constraints)
//         .then(stream => {
//             cameras[id].stream = stream;
//             cameras[id].videoElement.srcObject = stream;
//         })
//         .catch(err => {
//             console.error('Error accessing camera:', err);
//             alert('Could not access camera. Please check permissions or try a different browser.');
//         });
// }

// function captureDesktopImage(id) {
//     const video = cameras[id].videoElement;
//     const canvas = cameras[id].canvasElement;
//     const imagePreview = document.getElementById(`img_${id}`);
//     const previewContainer = document.getElementById(`preview_${id}`);
//     const hiddenInput = document.getElementById(`input_${id}`);
    
//     // Set canvas dimensions to match video
//     canvas.width = video.videoWidth;
//     canvas.height = video.videoHeight;
    
//     // Draw video frame to canvas
//     const ctx = canvas.getContext('2d');
//     ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
//     // Get image data as PNG for consistency with mobile version
//     const imageData = canvas.toDataURL('image/png');
    
//     // Update preview and hidden input
//     imagePreview.src = imageData;
//     previewContainer.style.display = "block";
//     hiddenInput.value = imageData;
// }

// function switchDesktopCamera(id) {
//     // Toggle facing mode
//     cameras[id].facingMode = cameras[id].facingMode === 'user' ? 'environment' : 'user';
//     console.log(`[${id}] Switched to ${cameras[id].facingMode === 'user' ? 'front' : 'back'} camera`);
    
//     // Restart camera with new facing mode
//     startCamera(id);
// }