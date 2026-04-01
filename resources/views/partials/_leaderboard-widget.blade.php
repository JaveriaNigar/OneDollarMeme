<div class="d-flex flex-column gap-3 sticky-top" style="top: 80px; z-index: 10; max-height: calc(100vh - 85px); overflow-y: auto;">

    @if(!isset($featuredBrand))
    <!-- BOX 1: ENTER BATTLE -->
    <div class="card border-0 shadow-sm rounded-4 text-white" style="background-color: var(--brand-orange);">
        <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
            <h5 class="fw-bold mb-1 text-uppercase">Upload & Compete</h5>
            <div class="fs-5 mb-2">${{ $sidebarPrizePool ?? '100' }} Prize</div>
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
    @endif

    <!-- BOX 2: WINNER SPOTLIGHT -->
    <!-- BOX 1.5: BRAND WINNERS -->
    @if(empty($hideBrandWinners))
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 text-center">
            <h6 class="fw-bold mb-0 text-uppercase small" style="color: var(--brand-orange);">🏆 BRAND WINNERS</h6>
        </div>
        <div class="card-body p-2">
            @if(isset($brandWinners) && count($brandWinners) > 0)
                <div class="list-group list-group-flush">
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
            @else
                <div class="text-center py-3 text-muted small">No brand winners yet</div>
            @endif
        </div>
    </div>
    @endif
    <!-- End BRAND WINNERS -->
    @if(empty($hideWinnerSpotlight))
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 text-center">
            <h6 class="fw-bold mb-0 text-uppercase small" style="color: var(--brand-purple);">
                @if(count($sidebarLastWeekWinners ?? []) > 1)
                    🏆 BATTLE LEADERBOARD
                @else
                    WINNER SPOTLIGHT
                @endif
            </h6>
        </div>
        <div class="card-body p-3 pt-0 text-center">
            @if(empty($sidebarLastWeekWinners) || count($sidebarLastWeekWinners) === 0)
                <div class="text-muted small py-4">No recent winners</div>
            @else
                @if(count($sidebarLastWeekWinners) === 1)
                    {{-- Single winner (previous challenge winner) --}}
                    @php $spotlight = $sidebarLastWeekWinners[0]; @endphp
                    <div class="p-3 bg-light rounded-3 mb-2 border">
                        <img src="{{ $spotlight->user_avatar }}" class="rounded-circle shadow-sm mb-2" style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="fw-bold text-dark">{{ $spotlight->user_name }}</div>
                    </div>
                    <div class="badge bg-white text-dark border shadow-sm px-3 py-2 rounded-pill">
                        <span class="text-purple fw-bold" style="color: var(--brand-purple);">{{ $spotlight->prize }}</span> Winner
                    </div>
                @else
                    {{-- Multiple leaders (current challenge) --}}
                    <div class="d-flex flex-column gap-2">
                        @foreach($sidebarLastWeekWinners as $index => $winner)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded {{ $index === 0 ? 'bg-warning bg-opacity-10 border border-warning' : 'bg-light' }}">
                                <div class="d-flex align-items-center">
                                    <span class="badge {{ $index === 0 ? 'bg-warning' : ($index === 1 ? 'bg-secondary' : 'bg-danger') }} rounded-circle me-2 fw-bold" style="width: 24px; height: 24px; padding: 0; line-height: 24px;">{{ $index + 1 }}</span>
                                    <img src="{{ $winner->user_avatar }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    <span class="fw-bold small">{{ $winner->user_name }}</span>
                                </div>
                                <span class="fw-bold small" style="color: var(--brand-purple);">{{ $winner->prize }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
    @endif


    <!-- BOX 3: TOP CREATORS -->
    @if(empty($hideTopCreators))
    <div class="card border-0 shadow-sm rounded-4" id="top-creators-card">
        <div class="card-header bg-white border-0 py-3 text-center">
            <h6 class="fw-bold mb-0 text-uppercase small" style="color: var(--brand-purple);">TOP CREATORS</h6>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush" id="top-creators-list">
                @if(empty($sidebarTop3) || count($sidebarTop3) === 0)
                    <div class="text-center p-3 text-muted small">No top creators yet</div>
                @else
                    @foreach($sidebarTop3 as $creator)
                        <div class="list-group-item border-0 d-flex align-items-center px-4 py-2" data-user-id="{{ $creator->user_id }}" data-username="{{ $creator->user_name }}">
                             <img src="{{ $creator->user_avatar }}" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;">
                             <div class="flex-grow-1">
                                 <span class="fw-bold small d-block">{{ $creator->user_name }}</span>
                                 <span style="font-size: 0.75rem; color: var(--brand-purple); font-weight: 800;">Score: <span class="creator-score-value">{{ $creator->score }}</span></span>
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
    @endif

    <!-- BOX 4: LIVE ACTIVITY -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 text-center">
             <h6 class="mb-0 text-uppercase" style="color: var(--brand-purple); font-weight: 900;">LIVE ACTIVITY</h6>
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
                });
        }
    });
}

// Function to update TOP CREATORS scores
function updateTopCreators() {
    fetch('/api/leaderboard')
        .then(response => response.json())
        .then(data => {
            if (data.top_contestants && data.top_contestants.length > 0) {
                const creatorsList = document.getElementById('top-creators-list');
                if (!creatorsList) return;

                // Get all creator items
                const creatorItems = creatorsList.querySelectorAll('[data-username]');
                
                // Update scores based on username match
                data.top_contestants.forEach((contestant, index) => {
                    creatorItems.forEach(item => {
                        const username = item.getAttribute('data-username');
                        if (username === contestant.user_name) {
                            const scoreElement = item.querySelector('.creator-score-value');
                            if (scoreElement) {
                                scoreElement.textContent = contestant.score;
                            }
                        }
                    });
                });
            }
        })
        .catch(error => {
            console.warn('Could not update top creators:', error);
        });
}

// Update scores every 5 seconds for instant updates
setInterval(updateLiveScores, 5000);
setInterval(updateTopCreators, 5000);
updateLiveScores(); // Initial update
updateTopCreators(); // Initial update
</script>
