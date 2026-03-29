<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Meme</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Emoji Picker -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

    <style>
        :root {
            --brand-purple: #6f42c1;
            --brand-purple-dark: #5a32a3;
            --brand-orange: #fd7e14;
            --brand-yellow: #ffc107;
            --bg-gradient: linear-gradient(135deg, #fdfbf7 0%, #f4f7fa 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            color: #333;
            padding-bottom: 40px;
        }

        /* Navbar Styling to match reference */
        .navbar {
            background-color: #3e1e86; /* Deep Purple */
            padding: 0.8rem 2rem;
        }
        .navbar-brand {
            color: white !important;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brand img {
            border-radius: 50%;
            border: 2px solid #fbbf24;
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            font-size: 0.95rem;
            margin-right: 1.5rem;
        }
        .nav-link.active, .nav-link:hover {
            color: white !important;
        }
        .btn-upload-nav {
            background-color: white;
            color: #3e1e86;
            font-weight: 700;
            border-radius: 8px;
            padding: 6px 16px;
        }
        .user-balance {
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Header Section */
        .page-header {
             /* Background simplified as a soft purple gradient area if needed, 
                but reference shows white/clean content area on a soft bg */
             padding: 40px 0 20px;
             text-align: left;
        }

        /* Main Layout */
        .main-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Left Card - Upload */
        .upload-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            height: 100%;
        }

        .upload-btn-wrapper {
            border: 2px dashed #d1d5db;
            border-radius: 16px;
            padding: 40px 20px;
            text-align: center;
            background-color: #f9fafb;
            transition: all 0.3s;
            cursor: pointer;
            margin-bottom: 2rem;
            position: relative;
        }
        .upload-btn-wrapper:hover {
            background-color: #f3f4f6;
            border-color: #a78bfa;
        }
        .file-input {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
            z-index: 10;
        }
        .choose-btn {
            background-color: #5b46e8;
            color: white;
            padding: 10px 30px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            margin-top: 15px;
            margin-bottom: 10px;
            display: inline-block;
        }

        /* Inputs */
        .custom-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-control-custom {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
        }
        .form-control-custom:focus {
            border-color: #5b46e8;
            box-shadow: 0 0 0 4px rgba(91, 70, 232, 0.1);
        }

        /* Buttons matching reference */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .btn-cancel {
            background: white;
            border: 1px solid #e5e7eb;
            color: #374151;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px;
        }
        .btn-free {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 8px;
            flex-grow: 1;
        }
        .btn-challenge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            flex-grow: 1.5;
            box-shadow: 0 4px 6px rgba(217, 119, 6, 0.2);
        }
        .btn-challenge:disabled, .btn-free:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            filter: grayscale(1);
        }

        /* Right Sidebar */
        .sidebar-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            height: fit-content;
        }
        .sidebar-title {
            color: #3e1e86;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tip-item {
            margin-bottom: 20px;
        }
        .tip-title {
            font-weight: 700;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        .tip-icon {
            font-size: 1.2rem;
        }
        .tip-desc {
            font-size: 0.85rem;
            color: #6b7280;
            padding-left: 28px;
            line-height: 1.4;
        }

        /* Preview */
        #previewArea {
            margin-top: 15px;
            border-radius: 12px;
            overflow: hidden;
            display: none;
            width: 100%;
        }
        #previewImage {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: contain;
            background: #000;
        }
        #previewCaption {
            white-space: pre-line;
            font-weight: 700;
            color: #111827;
            font-size: 1.1rem;
            padding: 10px 15px;
            background: #fff;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    @include('partials._toast')



    <!-- Page Content -->
    <div class="main-container py-5 px-3">
        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="fw-bold text-dark mb-1" style="font-size: 2rem;">Upload YOUR Meme</h1>
            <p class="text-muted" style="font-size: 1.05rem;">Share your best meme and compete for real cash prizes!</p>
        </div>

        <div class="row g-4">
            <!-- Left Column: Upload Form -->
            <div class="col-lg-8">
                <div class="upload-card">
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi bi-check-square-fill text-warning me-2 fs-4"></i>
                        <h4 class="fw-bold mb-0">Upload Meme</h4>
                    </div>
                    <p class="text-muted mb-4 small ms-4 ps-1">Make everyone laugh and win <span class="fw-bold text-dark">big rewards</span> by sharing your funniest meme.</p>

                    <form action="/upload-meme" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        
                        <!-- Drag & Drop Area -->
                        <div class="upload-btn-wrapper position-relative">
                            <input type="file" name="image" id="imageInput" class="file-input" accept="image/*">
                            
                            <!-- Initial View -->
                            <div id="uploadPlaceholder">
                                <div class="mb-3">
                                    <i class="bi bi-cloud-arrow-up-fill" style="font-size: 3rem; color: #a78bfa;"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Drag and drop a meme here or</h6>
                                <button type="button" class="choose-btn">Choose Meme</button>
                            </div>
                            
                            <!-- Preview View -->
                            <div id="previewArea">
                                <img id="previewImage" src="" alt="Preview">
                                <div id="previewCaption" style="display: none;"></div>
                                <div class="mt-2 text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="removeImageBtn">Remove & Change</button>
                                </div>
                            </div>
                        </div>

                        <!-- Spacer -->
                        <div class="mb-4"></div>

                        <!-- Caption Input -->
                        <div class="mb-4 position-relative">
                            <div class="d-flex justify-content-between">
                                <label class="custom-label">Add a caption to make your meme even funnier</label>
                                <span class="text-muted small" id="charCount">0 / 140</span>
                            </div>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary border-end-0" type="button" id="emojiToggleBtn" style="border-radius: 10px 0 0 10px; background: #f9fafb; border-color: #e5e7eb;">
                                    😀
                                </button>
                                <input type="text" name="title" id="titleInput" class="form-control form-control-custom border-start-0 ps-1" style="border-radius: 0 10px 10px 0;" placeholder="Enter a caption..." value="{{ request('title') !== 'Untitled' ? request('title') : '' }}">
                            </div>
                            <div id="emojiPickerContainer" style="display:none; position: absolute; top: 100%; left: 0; z-index: 1000; margin-top: 5px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <emoji-picker id="captionEmojiPicker"></emoji-picker>
                            </div>
                        </div>

                        <!-- Tags Input -->
                        <div class="mb-4 position-relative">
                             <div class="d-flex justify-content-between">
                                <label class="custom-label mb-1">
                                    Add Tags
                                </label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-3 ms-1" style="border-color: #e5e7eb; border-radius: 10px 0 0 10px;">
                                    <i class="bi bi-hash text-muted"></i>
                                </span>
                                <input type="text" name="tags" id="tagsInput" class="form-control form-control-custom border-start-0 ps-0" style="border-radius: 0 10px 10px 0;" placeholder="e.g. #funny #meme #viral">
                                <span class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted" style="cursor: pointer; z-index: 5;">&times;</span>
                            </div>
                        </div>

                        <!-- Brand Selection -->
                        @if(isset($brands) && $brands->count() > 0)
                        <div class="mb-4">
                            <label class="custom-label mb-1">Participate in Brand Campaign (Optional)</label>
                            <select name="brand_id" class="form-select form-control-custom" style="appearance: auto;">
                                <option value="">-- No, thanks --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Buttons -->
                        <div class="action-buttons">
                            <a href="{{ route('home') }}" class="btn btn-cancel text-decoration-none">Cancel</a>
                            
                            <button type="submit" name="action" value="upload" class="btn-free" id="submitBtn" disabled>
                                Upload Meme
                            </button>
                            
                            <button type="submit" name="action" value="pay" class="btn-challenge" id="payBtn" disabled>
                                Enter Challenge 
                            </button>
                        </div>

                        <div class="text-center mt-3 text-muted small" style="font-size: 0.75rem;">
                            By submitting, you agree to our <a href="#" class="text-warning text-decoration-none" data-bs-toggle="modal" data-bs-target="#termsModal">terms of service</a>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar-card pt-4">
                    <div class="sidebar-title">
                        <i class="bi bi-lightbulb-fill text-warning"></i> Winning Tips
                    </div>

                    <div class="tip-item">
                        <div class="tip-title">
                            <span class="fs-5">🚀</span> Post Memes Early
                        </div>
                        <div class="tip-desc">
                            Enter for $1 to appear in challenge and trending.
                        </div>
                    </div>

                    <div class="tip-item">
                        <div class="tip-title">
                            <span class="fs-5" style="color: #25d366;">💬</span> Share & Go Viral
                        </div>
                        <div class="tip-desc">
                            Share your meme across WhatsApp groups.
                        </div>
                    </div>

                     <div class="tip-item pb-2">
                        <div class="tip-title">
                            <span class="fs-5" style="color: #f97316;">🔥</span> Use Trending Captions
                        </div>
                        <div class="tip-desc">
                            Use popular themes + witty captions.
                        </div>
                    </div>

                    <a href="#" class="text-purple fw-bold text-decoration-none small" style="color: #6f42c1;" data-bs-toggle="modal" data-bs-target="#termsModal">View Full Rules →</a>
                </div>
            </div>
        </div>
    </div>

    @include('partials._terms-modal')

    <!-- Scripts -->
    <!-- Bootstrap JS Bundle (includes Popper) needed for modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const imageInput = document.getElementById('imageInput');
        const previewImage = document.getElementById('previewImage');
        const previewArea = document.getElementById('previewArea');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const removeImageBtn = document.getElementById('removeImageBtn');
        const submitBtn = document.getElementById('submitBtn');
        const payBtn = document.getElementById('payBtn');
        const titleInput = document.getElementById('titleInput');
        const charCount = document.getElementById('charCount');
        const emojiToggleBtn = document.getElementById('emojiToggleBtn');
        const emojiPickerContainer = document.getElementById('emojiPickerContainer');
        const captionEmojiPicker = document.getElementById('captionEmojiPicker');

        // Emoji Toggle
        emojiToggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            emojiPickerContainer.style.display = emojiPickerContainer.style.display === 'none' ? 'block' : 'none';
        });

        // Close picker on outside click
        document.addEventListener('click', (e) => {
            if (!emojiPickerContainer.contains(e.target) && e.target !== emojiToggleBtn) {
                emojiPickerContainer.style.display = 'none';
            }
        });

        // Insert Emoji
        captionEmojiPicker.addEventListener('emoji-click', event => {
            const emoji = event.detail.unicode;
            const start = titleInput.selectionStart;
            const end = titleInput.selectionEnd;
            const text = titleInput.value;
            titleInput.value = text.substring(0, start) + emoji + text.substring(end);
            titleInput.focus();
            titleInput.setSelectionRange(start + emoji.length, start + emoji.length);
            
            // Trigger input event to update char count and preview
            titleInput.dispatchEvent(new Event('input'));
        });

        // Character count
        titleInput.addEventListener('input', function() {
            const len = this.value.length;
            charCount.textContent = `${len} / 140`;
            if(len > 140) {
                charCount.style.color = 'red';
                this.classList.add('is-invalid');
            } else {
                charCount.style.color = '#6c757d';
                this.classList.remove('is-invalid');
            }
            updateSubmitButton();
            updateLivePreview();
        });

        const tagsInput = document.getElementById('tagsInput');
        tagsInput.addEventListener('input', updateLivePreview);

        function updateLivePreview() {
            const title = titleInput.value.trim();
            const tags = tagsInput.value.trim();
            const previewCaption = document.getElementById('previewCaption');
            
            if (title || tags) {
                let content = title;
                if (tags) {
                    content = title ? title + "\n" + tags : tags;
                }
                previewCaption.textContent = content;
                previewCaption.style.display = 'block';
            } else {
                previewCaption.style.display = 'none';
            }
        }

        // Image Handling
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                 if (validateImage(file)) {
                     showPreview(file);
                 } else {
                     if (window.showToast) window.showToast('Invalid file. Please use JPG, PNG or WebP max 2MB.', 'error');
                     resetImage();
                 }
            }
        });

        function showPreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewArea.style.display = 'block';
                uploadPlaceholder.style.display = 'none';
                updateSubmitButton();
            }
            reader.readAsDataURL(file);
        }

        function resetImage() {
            imageInput.value = '';
            previewArea.style.display = 'none';
            uploadPlaceholder.style.display = 'block';
            updateSubmitButton();
        }

        if(removeImageBtn) {
            removeImageBtn.addEventListener('click', function(e) {
                e.preventDefault();
                resetImage();
            });
        }

        function validateImage(file) {
            if (file.size > 2 * 1024 * 1024) return false;
            const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            return validTypes.includes(file.type);
        }

        function updateSubmitButton() {
            const hasTitle = titleInput.value.trim().length >= 3;
            const hasImage = imageInput.files.length > 0 || previewArea.style.display === 'block';

            // Enable if either title (>=3 chars) or image is present
            if (hasTitle || hasImage) {
                submitBtn.disabled = false;
                payBtn.disabled = false;
                submitBtn.style.opacity = '1';
                payBtn.style.opacity = '1';
            } else {
                submitBtn.disabled = true;
                payBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                payBtn.style.opacity = '0.5';
            }
        }

        // Restore preview from URL if redirected back
        const urlParams = new URLSearchParams(window.location.search);
        const imagePreviewUrl = urlParams.get('image_preview');
        if (imagePreviewUrl) {
           previewImage.src = imagePreviewUrl;
           previewArea.style.display = 'block';
           uploadPlaceholder.style.display = 'none';
           updateSubmitButton();
        }
        
        // Initial state check
        updateSubmitButton();
        if (titleInput.value) {
            charCount.textContent = `${titleInput.value.length} / 140`;
            updateLivePreview();
        }
        
        // Drag and drop styles interaction
        const wrapper = document.querySelector('.upload-btn-wrapper');
        ['dragenter', 'dragover'].forEach(eventName => {
            wrapper.addEventListener(eventName, () => {
                wrapper.style.backgroundColor = '#f3f4f6';
                wrapper.style.borderColor = '#6f42c1';
            }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            wrapper.addEventListener(eventName, () => {
                wrapper.style.backgroundColor = '#f9fafb';
                wrapper.style.borderColor = '#d1d5db';
            }, false);
        });

    });
    </script>
</body>
</html>
