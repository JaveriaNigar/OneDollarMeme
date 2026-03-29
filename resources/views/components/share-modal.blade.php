@props(['id' => 'share-modal', 'title' => 'Share'])

<div id="{{ $id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="bg-white rounded-lg p-6 w-80 max-w-sm mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-black">{{ $title }}</h3>
            <button onclick="closeShareModal('{{ $id }}')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="space-y-3">
            <button onclick="copyMemeLink(arguments[0], '{{ $id }}')" class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-100 rounded-lg">
                <i class="fas fa-copy text-gray-700"></i>
                <span>Copy Link</span>
            </button>
            <a href="#" onclick="shareOnFacebook(arguments[0], '{{ $id }}'); return false;" class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-100 rounded-lg block">
                <i class="fab fa-facebook text-blue-600"></i>
                <span>Share on Facebook</span>
            </a>
            <a href="#" onclick="shareOnWhatsApp(arguments[0], '{{ $id }}'); return false;" class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-100 rounded-lg block">
                <i class="fab fa-whatsapp text-green-500"></i>
                <span>Share on WhatsApp</span>
            </a>
        </div>
    </div>
</div>

<script>
function showMemeShareModal(event, memeId, modalId = 'share-modal') {
    event.preventDefault();
    
    // Get the meme URL
    const memeUrl = `{{ route("memes.show", ":id") }}`.replace(':id', memeId);
    const memeTitle = 'Check out this meme on OneDollarMeme!';
    
    // Update the modal's onclick handlers with the correct URL
    const modal = document.getElementById(modalId);
    if (modal) {
        // Update the data attributes with the meme URL
        modal.setAttribute('data-meme-url', memeUrl);
        modal.setAttribute('data-meme-title', memeTitle);
        
        // Update the href attributes for social media sharing
        const fbLink = modal.querySelector('a[onclick*="shareOnFacebook"]');
        const waLink = modal.querySelector('a[onclick*="shareOnWhatsApp"]');
        
        if (fbLink) {
            fbLink.setAttribute('data-url', memeUrl);
        }
        if (waLink) {
            waLink.setAttribute('data-url', encodeURIComponent(memeTitle + ' ' + memeUrl));
        }
        
        // Show the modal
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
}

function copyMemeLink(event, modalId = 'share-modal') {
    event.preventDefault();
    
    const modal = document.getElementById(modalId);
    const memeUrl = modal ? modal.getAttribute('data-meme-url') : null;
    
    if (memeUrl) {
        navigator.clipboard.writeText(memeUrl).then(function() {
            if (window.showToast) {
                window.showToast('Link copied to clipboard!', 'success');
            }
            closeShareModal(modalId);
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = memeUrl;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            if (window.showToast) {
                window.showToast('Link copied to clipboard!', 'success');
            }
            closeShareModal(modalId);
        });
    }
}

function shareOnFacebook(event, modalId = 'share-modal') {
    event.preventDefault();
    
    const modal = document.getElementById(modalId);
    const memeUrl = modal ? modal.getAttribute('data-meme-url') : null;
    
    if (memeUrl) {
        const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(memeUrl)}`;
        window.open(facebookUrl, '_blank');
        closeShareModal(modalId);
    }
}

function shareOnWhatsApp(event, modalId = 'share-modal') {
    event.preventDefault();
    
    const modal = document.getElementById(modalId);
    const memeUrl = modal ? modal.getAttribute('data-meme-url') : null;
    const memeTitle = modal ? modal.getAttribute('data-meme-title') : 'Check out this meme on OneDollarMeme!';
    
    if (memeUrl) {
        const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(memeTitle + ' ' + memeUrl)}`;
        window.open(whatsappUrl, '_blank');
        closeShareModal(modalId);
    }
}

function closeShareModal(modalId = 'share-modal') {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}

// Close modal when clicking on the backdrop
document.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('[id*="share-modal"]');
    modals.forEach(modal => {
        if (modal && modal.classList.contains('hidden') === false && 
            !modal.querySelector('div').contains(event.target) &&
            event.target.id !== modal.id) {
            closeShareModal(modal.id);
        }
    });
});
</script>