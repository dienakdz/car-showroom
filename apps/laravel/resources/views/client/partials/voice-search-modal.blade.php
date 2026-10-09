@php
    $isTestOpen = request('test_voice') === 'open' || request('test_voice') === 'listening';
    $isTestListening = request('test_voice') === 'listening';
@endphp
<div id="voiceSearchModal" class="voice-search-overlay js-voice-overlay {{ $isTestOpen ? 'is-open' : '' }}" data-inventory-url="{{ route('inventory.index') }}" aria-hidden="{{ $isTestOpen ? 'false' : 'true' }}" role="dialog" aria-modal="true" aria-labelledby="voiceSearchTitle">
    <div class="voice-search-backdrop js-voice-modal-close"></div>
    <div class="voice-search-dialog">
        {{-- Close Button --}}
        <button type="button" class="voice-close-btn js-voice-modal-close" aria-label="Đóng tìm kiếm giọng nói">
            <i class="fa-solid fa-xmark"></i>
        </button>

        {{-- Header --}}
        <div class="voice-dialog-header text-center">
            <span class="voice-dialog-badge">
                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Voice Search AI
            </span>
            <h4 id="voiceSearchTitle" class="voice-dialog-title js-voice-dialog-title">Tìm kiếm bằng giọng nói</h4>
            <p class="voice-dialog-subtitle js-voice-dialog-subtitle">Nói tên hãng xe, dòng xe, khoảng giá hoặc nhu cầu của bạn</p>
        </div>

        {{-- Center Interactive Mic Visualizer --}}
        <div class="voice-visualizer-container">
            <button type="button" class="voice-mic-main-btn js-voice-main-mic-btn {{ $isTestListening ? 'is-listening' : 'is-idle' }}" title="Bấm để bật / tắt micro" aria-label="Micro thu âm">
                <div class="pulse-ring ring-1"></div>
                <div class="pulse-ring ring-2"></div>
                <div class="pulse-ring ring-3"></div>
                
                {{-- State 1: Idle Icon --}}
                <span class="mic-icon-wrapper mic-icon-idle" style="{{ $isTestListening ? 'display: none;' : '' }}">
                    <i class="fa-solid fa-microphone"></i>
                </span>

                {{-- State 2: Listening Icon --}}
                <span class="mic-icon-wrapper mic-icon-listening" style="{{ $isTestListening ? 'display: inline-block;' : 'display: none;' }}">
                    <i class="fa-solid fa-microphone-lines"></i>
                </span>
            </button>

            {{-- Soundwave Equalizer (Visible when listening) --}}
            <div class="voice-equalizer js-voice-equalizer" style="{{ $isTestListening ? 'display: flex;' : 'display: none;' }}">
                <span class="eq-bar bar-1"></span>
                <span class="eq-bar bar-2"></span>
                <span class="eq-bar bar-3"></span>
                <span class="eq-bar bar-4"></span>
                <span class="eq-bar bar-5"></span>
                <span class="eq-bar bar-6"></span>
                <span class="eq-bar bar-7"></span>
            </div>
        </div>

        {{-- Status and Live Transcript Section --}}
        <div class="voice-transcript-wrapper">
            <div class="voice-listening-status js-voice-status {{ $isTestListening ? 'status-listening' : '' }}">
                <span class="status-indicator"></span>
                <span class="status-text">{{ $isTestListening ? 'Đang lắng nghe bạn nói...' : 'Sẵn sàng lắng nghe' }}</span>
            </div>

            <div class="voice-transcript-box">
                <p class="voice-transcript-text js-voice-transcript {{ $isTestListening ? '' : 'placeholder-active' }}">
                    {{ $isTestListening ? '"Mercedes C300 màu đen"' : '"Hãy nói: Mercedes C300, Mazda CX-5, Xe 7 chỗ..."' }}
                </p>
            </div>
        </div>

        {{-- Modal Actions --}}
        <div class="voice-dialog-actions">
            {{-- Primary Action Button (Switches between Stop & Search and Search Now) --}}
            <button type="button" class="voice-action-btn {{ $isTestListening ? 'voice-btn-listening' : 'voice-btn-primary' }} js-voice-primary-action-btn">
                <i class="fa-solid {{ $isTestListening ? 'fa-circle-stop' : 'fa-magnifying-glass' }} me-2 voice-action-icon"></i>
                <span class="voice-action-label">{{ $isTestListening ? 'Dừng nói & Tìm kiếm' : 'Tìm kiếm ngay' }}</span>
            </button>

            {{-- Secondary Retry Button (Visible when stopped) --}}
            <button type="button" class="voice-action-btn voice-btn-secondary js-voice-retry-btn" style="display: none;">
                <i class="fa-solid fa-rotate-right me-1"></i> Nói lại
            </button>
        </div>
    </div>
</div>
