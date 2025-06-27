<style>
    .video-responsive {
        position: relative;
        padding: 25%; /* 16:9 aspect ratio */
        height: 10;
        overflow: hidden;
        border-radius: 8px;
    }

    .video-responsive iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    .youtube-slider {
        display: flex;
        overflow-x: auto; /* Enable horizontal scrolling */
        scroll-snap-type: x mandatory; /* Snap to slides */
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
        scrollbar-width: none; /* Hide scrollbar for Firefox */
        -ms-overflow-style: none;  /* Hide scrollbar for IE/Edge */
        gap: 20px; /* Space between videos */
        padding-bottom: 10px; /* For potential scrollbar space on some systems */
    }

    /* Hide scrollbar for Chrome, Safari, Opera */
    .youtube-slider::-webkit-scrollbar {
        display: none;
    }
</style>

<div class="banner">
    <div class="container">
        <div class="product-header">
            <h2 style="text-align: center" class="product-title">Trailer mới nhất</h2>
            <span></span>
        </div>
        <div class="slider-container has-scrollbar">
            <div class="slider-item">
                <div class="video-responsive">
                    <div class="youtube-slider">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/bW1m_qzi4EM?si=GVA2PnsPbdLrkW1y" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            <div class="slider-item">
                <div class="video-responsive">
                    <div class="youtube-slider">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/6MstABFeCME?si=pSR0XeTGOdIEz0Zp" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            <div class="slider-item">
                <div class="video-responsive">
                    <div class="youtube-slider">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/mW9OQxmeZCg?si=8YKhN1m9gF1ig6rg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
