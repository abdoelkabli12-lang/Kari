<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .upload-container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background-color: #fafafa;
        }

        .upload-area:hover {
            border-color: #4a90e2;
            background-color: #f0f7ff;
        }

        .upload-area.drag-over {
            border-color: #4a90e2;
            background-color: #e8f0fe;
            transform: scale(1.02);
        }

        .upload-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background-color: #e8f0fe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upload-icon svg {
            width: 30px;
            height: 30px;
            fill: #4a90e2;
        }

        .upload-text h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .upload-text p {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .file-name {
            font-size: 14px;
            color: #333;
            margin-top: 15px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 4px;
            display: none;
        }

        .file-name.show {
            display: block;
        }

        .choose-file-btn {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .choose-file-btn:hover {
            background-color: #3a7bc8;
        }

        .supported-formats {
            margin-top: 20px;
            font-size: 13px;
            color: #888;
        }

        .supported-formats strong {
            color: #666;
        }

        .file-input {
            display: none;
        }

        .uploaded-file {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #4a90e2;
            display: none;
        }

        .uploaded-file.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .uploaded-file-header {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .file-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .file-icon svg {
            fill: #4a90e2;
        }

        .uploaded-file-name {
            font-weight: 500;
            color: #333;
        }

        .uploaded-file-size {
            font-size: 12px;
            color: #888;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

<div class="max-w-3xl mx-auto py-10 px-6">

    <div class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Add New Rental</h1>

        <form action = "save_rentals.php" method = "POST" class="space-y-5" enctype="multipart/form-data">

            <!-- Host Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Host Name</label>
                <input type="text"
                       placeholder="John Doe"
                       name = "hostName"
                       class="mt-1 w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Rental Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Rental Type</label>
                <select class="mt-1 w-full border rounded-lg px-4 py-2" name = "rentalType">
                    <option>House</option>
                    <option>Villa</option>
                    <option>Hotel</option>
                    <option>Apartment</option>
                </select>
            </div>

            <!-- Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Price per Night ($)</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number"
                           step="0.01"
                           min="0"
                           placeholder="0.00"
                           class="block w-full pl-7 pr-12 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500"
                           name = "price"
                           aria-describedby="price-currency">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm" id="price-currency">USD</span>
                    </div>
                </div>
                <p class="mt-1 text-sm text-gray-500">Enter the price per night in US dollars</p>
            </div>

            <!-- Images -->
            <div class="upload-container">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M8,15V17H16V15H8Z"/>
                        </svg>
                    </div>
                    <div class="upload-text">
                        <h3>Upload file</h3>
                        <p>Drag and Drop file here or</p>
                        <button class="choose-file-btn" id="chooseFileBtn">Choose file</button>
                        <div class="supported-formats">
                            Supported formats: <strong>JPG, PNG, GIF, WEBP</strong><br>
                            Maximum size: <strong>25MB</strong>
                        </div>
                    </div>
                </div>

                <div class="uploaded-file" id="uploadedFile">
                    <div class="uploaded-file-header">
                        <div class="file-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M8,15V17H16V15H8Z"/>
                            </svg>
                        </div>
                        <span class="uploaded-file-name" id="uploadedFileName">image.png</span>
                    </div>
                    <div class="uploaded-file-size" id="uploadedFileSize">2.4 MB</div>
                </div>

                <input type="file" name = "image" id="fileInput" class="file-input" accept="image/*" />
            </div>



            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name = "startDate"
                           class="mt-1 w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name = "endDate"
                           class="mt-1 w-full border rounded-lg px-4 py-2">
                </div>
            </div>

            <!-- Location -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Location</label>
                <input type="text"
                       name="location"
                       placeholder="City, Country"
                       class="mt-1 w-full border rounded-lg px-4 py-2">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea rows="4" name="description"
                          placeholder="Describe your rental..."
                          class="mt-1 w-full border rounded-lg px-4 py-2"></textarea>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition">
                Add Rental
            </button>

        </form>
    </div>

</div>

<script>
    const uploadArea = document.getElementById('uploadArea');
    const chooseFileBtn = document.getElementById('chooseFileBtn');
    const fileInput = document.getElementById('fileInput');
    const uploadedFile = document.getElementById('uploadedFile');
    const uploadedFileName = document.getElementById('uploadedFileName');
    const uploadedFileSize = document.getElementById('uploadedFileSize');

    // Handle drag and drop events
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');

        if (e.dataTransfer.files.length) {
            handleFileSelect(e.dataTransfer.files[0]);
        }
    });

    // Handle click on upload area and button
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    chooseFileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    // Handle file input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Handle file selection
    function handleFileSelect(file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, GIF, WEBP)');
            return;
        }

        // Validate file size (25MB)
        const maxSize = 25 * 1024 * 1024; // 25MB in bytes
        if (file.size > maxSize) {
            alert('File size exceeds 25MB limit');
            return;
        }

        // Update UI with selected file info
        uploadedFileName.textContent = file.name;
        uploadedFileSize.textContent = formatFileSize(file.size);
        uploadedFile.classList.add('show');

        // You can now handle the file upload here
        // For example, using FormData and fetch API
        console.log('Selected file:', file);

        // If you want to preview the image:
        // previewImage(file);
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Optional: Preview image function
    function previewImage(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Create image preview
            const preview = document.createElement('img');
            preview.src = e.target.result;
            preview.style.maxWidth = '200px';
            preview.style.maxHeight = '200px';
            preview.style.marginTop = '20px';
            preview.style.borderRadius = '4px';

            // Remove existing preview if any
            const existingPreview = uploadArea.querySelector('img');
            if (existingPreview) {
                existingPreview.remove();
            }

            uploadArea.appendChild(preview);
        };
        reader.readAsDataURL(file);
    }
</script>
</body>
</html>