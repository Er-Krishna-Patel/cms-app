/**
 * Media Upload Handler
 * Handles multi-file uploads with instant preview and progress tracking
 */

window.handleUpload = function(event) {
    const files = Array.from(event.target.files);
    if (files.length === 0) return;

    const mainEl = document.querySelector('[x-data]');
    if (!mainEl) return;

    // Get Alpine component instance
    const alpine = Alpine.findClosest(mainEl, el => el.__x);
    if (!alpine) return;

    const state = alpine.$data;

    // Initialize upload tracking
    const uploadingFiles = [];
    const fileObjects = [];

    // Create file tracking objects with instant previews
    files.forEach((file, index) => {
        const fileId = Date.now() + index;
        const fileObj = {
            id: fileId,
            name: file.name,
            progress: 0,
            preview: null,
            icon: getFileIcon(getFileType(file))
        };
        
        // Generate instant preview for images
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                fileObj.preview = e.target.result;
                state.uploadingFiles = [...uploadingFiles];
            };
            reader.readAsDataURL(file);
        }

        uploadingFiles.push(fileObj);
        fileObjects.push(file);
    });

    // Update Alpine state with all files
    state.uploadingFiles = uploadingFiles;
    state.uploadError = false;
    state.uploadMessage = '';
    state.totalUploadProgress = 0;

    // Upload each file with individual progress tracking
    fileObjects.forEach((file, index) => {
        uploadFileWithProgress(file, index, uploadingFiles, state);
    });

    // Reset file input
    event.target.value = '';
};

function uploadFileWithProgress(file, fileIndex, uploadingFiles, state) {
    const formData = new FormData();
    formData.append('files[]', file);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.content);
    }

    const xhr = new XMLHttpRequest();

    // Track upload progress
    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const progress = Math.round((e.loaded / e.total) * 100);
            uploadingFiles[fileIndex].progress = progress;
            
            // Calculate total progress
            const totalProgress = uploadingFiles.reduce((sum, f) => sum + f.progress, 0) / uploadingFiles.length;
            state.totalUploadProgress = Math.round(totalProgress);
            state.uploadingFiles = [...uploadingFiles];
        }
    });

    // Handle completion
    xhr.addEventListener('load', () => {
        if (xhr.status === 200 || xhr.status === 201) {
            uploadingFiles[fileIndex].progress = 100;
            
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.uploaded && response.uploaded.length > 0) {
                    // Add to allMedia array immediately
                    const uploadedFile = response.uploaded[0];
                    state.allMedia = [{
                        id: uploadedFile.id,
                        name: uploadedFile.original_name,
                        type: uploadedFile.file_type,
                        url: uploadedFile.url,
                        thumb_url: uploadedFile.thumb_url,
                        medium_url: uploadedFile.medium_url,
                        large_url: uploadedFile.large_url,
                        is_image: uploadedFile.is_image,
                        size: uploadedFile.human_readable_size,
                        date: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }),
                        alt_text: uploadedFile.alt_text || '',
                        caption: uploadedFile.caption || '',
                        description: uploadedFile.description || '',
                        mime_type: uploadedFile.mime_type,
                        width: uploadedFile.width || null,
                        height: uploadedFile.height || null,
                        created_at: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
                    }, ...state.allMedia];
                }
            } catch (e) {
                console.error('Failed to parse response', e);
            }
        } else {
            state.uploadError = true;
            state.uploadMessage = `Error uploading ${file.name}`;
            console.error('Upload error:', xhr.responseText);
        }

        // Update progress to complete
        uploadingFiles[fileIndex].progress = 100;
        state.uploadingFiles = [...uploadingFiles];

        // Check if all files are done
        const allDone = uploadingFiles.every(f => f.progress === 100);
        if (allDone) {
            setTimeout(() => {
                if (!state.uploadError) {
                    state.uploadMessage = uploadingFiles.length + ' file(s) uploaded successfully!';
                    state.uploadingFiles = [];
                }
            }, 500);
        }
    });

    // Handle errors
    xhr.addEventListener('error', () => {
        state.uploadError = true;
        state.uploadMessage = `Error uploading ${file.name}`;
        uploadingFiles[fileIndex].progress = 0;
        state.uploadingFiles = [...uploadingFiles];
    });

    // Get the upload route from the page
    const uploadUrl = document.querySelector('[data-upload-url]')?.dataset.uploadUrl || '/admin/media';
    xhr.open('POST', uploadUrl, true);
    xhr.send(formData);
}

function getFileType(file) {
    const type = file.type.split('/')[0];
    if (type === 'image') return 'image';
    if (type === 'video') return 'video';
    if (type === 'audio') return 'audio';
    if (file.type === 'application/pdf') return 'pdf';
    if (file.type.includes('word') || file.type.includes('document')) return 'document';
    if (file.type.includes('sheet')) return 'spreadsheet';
    return 'default';
}

function getFileIcon(type) {
    const icons = {
        'image': '🖼️',
        'video': '🎬',
        'audio': '🎵',
        'pdf': '📄',
        'document': '📝',
        'spreadsheet': '📊',
        'default': '📎'
    };
    return icons[type] || icons['default'];
}

function truncate(str, length) {
    return str.length > length ? str.substring(0, length) + '...' : str;
}