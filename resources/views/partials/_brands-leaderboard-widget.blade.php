<div class="d-flex flex-column gap-3 sticky-top" style="top: 80px; z-index: 10; max-height: calc(100vh - 85px); overflow-y: auto;">

    <!-- BOX 1: ENTER BATTLE -->
    <div class="card border-0 shadow-sm rounded-4 text-white" style="background-color: var(--brand-orange);">
        <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
            <h5 class="fw-bold mb-1 text-uppercase">Upload & Compete</h5>
            <div class="fs-5 mb-2">$100 Prize</div>
            <div class="small fw-bold mb-3 text-white">Ends: <span class="js-battle-timer" data-end-time="{{ $sidebarEndTime ?? '' }}">Loading...</span></div>
            @auth
            <a href="{{ route('upload-meme.create') }}" class="btn btn-sm fw-bold w-100 py-1 shadow-sm" style="background-color: var(--brand-purple); color: white; border-radius: 6px; font-size: 0.85rem;">
               Upload & Compete
            </a>
            @else
            <a href="{{ route('login') }}" class="btn btn-sm fw-bold w-100 py-1 shadow-sm" style="background-color: #6c757d; color: white; border-radius: 6px; font-size: 0.85rem;">
                LOGIN TO JOIN
            </a>
            @endauth
        </div>
    </div>

    <!-- BOX 2: WINNER SPOTLIGHT -->
    <!-- BOX 1.5: BRAND WINNERS -->
    @if(isset($brandWinners) && count($brandWinners) > 0)
    <div class="card border-0 shadow-sm rounded-4" id="brand-winners-card">
        <div class="card-header bg-white border-0 py-3 text-center">
            <h6 class="fw-bold mb-0 text-uppercase small" style="color: var(--brand-orange);">🏆 BRAND WINNERS</h6>
        </div>
        <div class="card-body p-2">
            <div class="list-group list-group-flush" id="brand-winners-list">
                @foreach(array_slice($brandWinners, 0, 3) as $brandId => $brandData)
                    @php
                        $brand = $brandData['brand'];
                        $winners = $brandData['winners'];
                    @endphp
                    <div class="list-group-item border-0 p-3 bg-light mb-2 rounded-3">
                        <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->company_name }}" class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                            @else
                                <div class="rounded me-2 d-flex align-items-center justify-content-center text-white" style="width: 30px; height: 30px; background-color: var(--brand-purple); font-size: 0.8rem;">
                                    {{ strtoupper(substr($brand->company_name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">{{ $brand->company_name }}</h6>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ count($winners) }} winners</small>
                            </div>
                        </div>
                        @foreach($winners as $index => $winner)
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="d-flex align-items-center">
                                    <span class="badge {{ $index === 0 ? 'bg-warning' : ($index === 1 ? 'bg-secondary' : 'bg-danger') }} rounded-circle me-2" style="width: 20px; height: 20px; padding: 0; line-height: 20px; text-align: center;">{{ $index + 1 }}</span>
                                    <span class="fw-bold" style="font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100px;">{{ $winner->user->name }}</span>
                                </div>
                                <span class="fw-bold" style="color: #fd7e14; font-size: 0.85rem;">Score: {{ $winner->calculated_score }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- End BRAND WINNERS -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 text-center">
            <h6 class="fw-bold mb-0 text-uppercase small" style="color: var(--brand-purple);">WINNER SPOTLIGHT</h6>
        </div>
        <div class="card-body p-3 pt-0 text-center">
            @if(empty($sidebarLastWeekWinners) || count($sidebarLastWeekWinners) === 0)
                <div class="text-muted small py-4">No recent winners</div>
            @else
                @php $spotlight = $sidebarLastWeekWinners[0]; @endphp
                <div class="p-3 bg-light rounded-3 mb-2 border">
                    <img src="{{ $spotlight->user_avatar }}" class="rounded-circle shadow-sm mb-2" style="width: 50px; height: 50px; object-fit: cover;">
                    <div class="fw-bold text-dark">{{ $spotlight->user_name }}</div>
                </div>
                <div class="badge bg-white text-dark border shadow-sm px-3 py-2 rounded-pill">
                    <span class="text-purple fw-bold" style="color: var(--brand-purple);">{{ $spotlight->prize }}</span> Winner
                </div>
            @endif
        </div>
    </div>


    <!-- BOX 3: TOP CREATORS -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 text-center">
            <h6 class="fw-bold mb-0 text-uppercase small" style="color: var(--brand-purple);">TOP CREATORS</h6>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @if(empty($sidebarTop3) || count($sidebarTop3) === 0)
                    <div class="text-center p-3 text-muted small">No top creators yet</div>
                @else
                    @foreach($sidebarTop3 as $creator)
                        <div class="list-group-item border-0 d-flex align-items-center px-4 py-2">
                             <img src="{{ $creator->user_avatar }}" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;">
                             <div class="flex-grow-1">
                                 <span class="fw-bold small d-block">{{ $creator->user_name }}</span>
                                 <span class="fw-bold" style="color: #fd7e14; font-size: 0.85rem;">Score: {{ $creator->score }}</span>
                             </div>
                             @auth
                             <button class="btn btn-sm btn-light border fw-bold px-3 py-0 small" style="color: var(--brand-purple); font-size: 0.75rem;">Follow</button>
                             @endauth
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- BOX 4: LIVE ACTIVITY -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 text-center">
             <h6 class="fw-bold mb-0 text-uppercase small" style="color: var(--brand-purple);">LIVE ACTIVITY</h6>
        </div>
        <div class="card-body p-0">
             <ul class="list-group list-group-flush small" id="live-activity-list">
                @php
                    $topPaidMemes = $sidebarLiveActivity ?? collect();
                @endphp

                @if($topPaidMemes->isEmpty())
                     <li class="list-group-item border-0 px-4 py-2 text-muted">No paid memes yet</li>
                @else
                    @foreach($topPaidMemes as $memeData)
                        <li class="list-group-item border-0 px-4 py-2" data-meme-id="{{ $memeData['meme_id'] }}">
                            <a @auth href="{{ route('home', ['highlight' => $memeData['meme_id']]) }}#meme-{{ $memeData['meme_id'] }}" @else href="javascript:void(0)" style="cursor: default;" @endauth class="text-decoration-none text-reset d-block">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <!-- Small green circle on the left side -->
                                        <span class="rounded-circle me-3 flex-shrink-0" style="width: 12px; height: 12px; background-color: #28a745;"></span>
                                        <div class="d-flex flex-column lh-sm">
                                            <span class="fw-bold">{{ $memeData['user'] }}</span>
                                            <span class="text-muted" style="font-size: 0.85rem;">{{ $memeData['title'] }}</span>
                                             <span class="fw-bold" style="color: #fd7e14; font-size: 0.9rem;">Score: <span class="score-value">{{ $memeData['score'] }}</span></span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        @if(isset($memeData['meme']) && $memeData['meme']->image_path)
                                            <img src="{{ asset('storage/'.$memeData['meme']->image_path) }}"
                                                 class="rounded-circle ms-3 flex-shrink-0"
                                                 style="width: 30px; height: 30px; object-fit: cover;"
                                                 alt="Meme thumbnail">
                                        @else
                                            <span class="rounded-circle ms-3 flex-shrink-0" style="width: 30px; height: 30px; background-color: #e9ecef;"></span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                @endif
             </ul>
        </div>
    </div>

</div>


<script>
// Function to update live scores in the LIVE ACTIVITY box
function updateLiveScores() {
    // Get all meme IDs from the live activity list
    const activityItems = document.querySelectorAll('#live-activity-list li[data-meme-id]');

    activityItems.forEach(item => {
        const memeId = item.getAttribute('data-meme-id');
        if (memeId) {
            // Make an AJAX request to get updated score for this meme
            fetch(`/api/meme/${memeId}/score`)
                .then(response => {
                    // Check if the response is OK (status 200-299)
                    if (!response.ok) {
                        // If the meme doesn't exist (404), remove the item from the list
                        if (response.status === 404) {
                            const listItem = document.querySelector(`#live-activity-list li[data-meme-id="${memeId}"]`);
                            if (listItem) {
                                listItem.remove();
                            }
                        }
                        return Promise.reject('Response not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Check if the item still exists in the DOM before trying to update it
                    const listItem = document.querySelector(`#live-activity-list li[data-meme-id="${memeId}"]`);
                    if (!listItem) {
                        return; // Exit early if item has been removed
                    }

                    if (data.score !== undefined) {
                        // Update the score in the UI
                        const scoreElement = listItem.querySelector('.score-value');
                        if (scoreElement) {
                            scoreElement.textContent = data.score;
                        }
                    }
                })
                .catch(error => {
                    console.warn(`Could not update score for meme ${memeId}:`, error);
                    // If there's an error (like JSON parsing error due to HTML response),
                    // it means the meme probably doesn't exist anymore, so remove the item
                    const listItem = document.querySelector(`#live-activity-list li[data-meme-id="${memeId}"]`);
                    if (listItem) {
                        listItem.remove();
                    }
                });
        }
    });
}

// Function to update BRAND WINNERS section
function updateBrandWinners() {
    console.log('updateBrandWinners called');
    fetch('/api/brand-winners')
        .then(response => {
            if (!response.ok) {
                return Promise.reject('Response not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Brand winners data received:', data);
            const listGroup = document.getElementById('brand-winners-list');
            console.log('brand-winners-list element:', listGroup);
            if (!listGroup) return;

            if (data.winners && Object.keys(data.winners).length > 0) {
                console.log('Updating brand winners with data:', Object.keys(data.winners).length, 'brands');
                // Build new content
                let html = '';
                const brandIds = Object.keys(data.winners);
                const maxBrands = Math.min(3, brandIds.length);

                for (let i = 0; i < maxBrands; i++) {
                    const brandId = brandIds[i];
                    const brandData = data.winners[brandId];
                    const brand = brandData.brand;
                    const winners = brandData.winners;

                    html += `<div class="list-group-item border-0 p-3 bg-light mb-2 rounded-3">`;
                    html += `<div class="d-flex align-items-center mb-2 pb-2 border-bottom">`;

                    if (brand.logo) {
                        html += `<img src="/storage/${brand.logo}" alt="${brand.company_name}" class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">`;
                    } else {
                        const initial = brand.company_name.charAt(0).toUpperCase();
                        html += `<div class="rounded me-2 d-flex align-items-center justify-content-center text-white" style="width: 30px; height: 30px; background-color: var(--brand-purple); font-size: 0.8rem;">${initial}</div>`;
                    }

                    html += `<div>`;
                    html += `<h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">${brand.company_name}</h6>`;
                    html += `<small class="text-muted" style="font-size: 0.7rem;">${winners.length} winners</small>`;
                    html += `</div>`;
                    html += `</div>`;

                    winners.forEach((winner, index) => {
                        const badgeClass = index === 0 ? 'bg-warning' : (index === 1 ? 'bg-secondary' : 'bg-danger');
                        html += `<div class="d-flex align-items-center justify-content-between mb-1">`;
                        html += `<div class="d-flex align-items-center">`;
                        html += `<span class="badge ${badgeClass} rounded-circle me-2" style="width: 20px; height: 20px; padding: 0; line-height: 20px; text-align: center;">${index + 1}</span>`;
                        html += `<span class="fw-bold" style="font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100px;">${winner.user_name}</span>`;
                        html += `</div>`;
                        html += `<span class="fw-bold" style="color: #fd7e14; font-size: 0.85rem;">Score: ${winner.calculated_score}</span>`;
                        html += `</div>`;
                    });

                    html += `</div>`;
                }

                listGroup.innerHTML = html;
                console.log('Brand winners updated successfully');
            }
        })
        .catch(error => {
            console.warn('Could not update brand winners:', error);
        });
}

// Update scores every 10 seconds to match live behavior
setInterval(updateLiveScores, 10000);
setInterval(updateBrandWinners, 10000);
updateLiveScores(); // Initial update
updateBrandWinners(); // Initial update
</script>
