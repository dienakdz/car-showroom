/**
 * Voice Search Module for BoxCar Showroom
 * Powered by Web Speech API (SpeechRecognition / webkitSpeechRecognition)
 */
(function () {
    'use strict';

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    const VoiceSearch = {
        recognition: null,
        isListening: false,
        activeInput: null,
        activeTriggerBtn: null,
        transcript: '',
        interimTranscript: '',
        autoSubmitTimer: null,

        // DOM elements
        modal: null,
        modalTitle: null,
        modalSubtitle: null,
        modalTranscript: null,
        modalStatus: null,
        mainMicBtn: null,
        equalizer: null,
        primaryActionBtn: null,
        retryBtn: null,
        allVoiceBtns: [],

        init: function () {
            this.cacheDOM();
            this.bindEvents();
            this.initSpeechEngine();
        },

        cacheDOM: function () {
            this.modal = document.getElementById('voiceSearchModal');
            if (!this.modal) return;

            this.modalTitle = this.modal.querySelector('.js-voice-dialog-title');
            this.modalSubtitle = this.modal.querySelector('.js-voice-dialog-subtitle');
            this.modalTranscript = this.modal.querySelector('.js-voice-transcript');
            this.modalStatus = this.modal.querySelector('.js-voice-status');
            this.mainMicBtn = this.modal.querySelector('.js-voice-main-mic-btn');
            this.equalizer = this.modal.querySelector('.js-voice-equalizer');
            this.primaryActionBtn = this.modal.querySelector('.js-voice-primary-action-btn');
            this.retryBtn = this.modal.querySelector('.js-voice-retry-btn');
            this.allVoiceBtns = Array.from(document.querySelectorAll('.js-voice-search-btn'));
            this.inventoryUrl = this.modal.getAttribute('data-inventory-url') || '/kho-xe';
        },

        initSpeechEngine: function () {
            if (!SpeechRecognition) {
                console.warn('[VoiceSearch] Web Speech API is not supported in this browser.');
                return;
            }

            try {
                this.recognition = new SpeechRecognition();
                this.recognition.continuous = false;
                this.recognition.interimResults = true;
                this.recognition.lang = 'vi-VN';
                this.recognition.maxAlternatives = 1;

                this.bindSpeechEvents();
            } catch (err) {
                console.error('[VoiceSearch] Error initializing SpeechRecognition:', err);
                this.recognition = null;
            }
        },

        bindSpeechEvents: function () {
            if (!this.recognition) return;

            this.recognition.onstart = () => {
                this.isListening = true;
                this.setListeningUIState(true);
            };

            this.recognition.onresult = (event) => {
                let interim = '';
                let final = '';

                for (let i = event.resultIndex; i < event.results.length; i++) {
                    const text = event.results[i][0].transcript;
                    if (event.results[i].isFinal) {
                        final += text;
                    } else {
                        interim += text;
                    }
                }

                if (final) {
                    this.transcript = final.trim();
                    this.updateTranscriptUI(this.transcript, false);

                    // When final speech result is acquired, wait brief moment then finish & search
                    clearTimeout(this.autoSubmitTimer);
                    this.autoSubmitTimer = setTimeout(() => {
                        if (this.isListening && this.transcript) {
                            this.stopListening(true);
                        }
                    }, 1100);
                } else if (interim) {
                    this.interimTranscript = interim.trim();
                    this.updateTranscriptUI(this.interimTranscript, true);
                }
            };

            this.recognition.onerror = (event) => {
                console.warn('[VoiceSearch] Recognition error:', event.error);
                clearTimeout(this.autoSubmitTimer);

                let errorMsg = 'Không nhận diện được giọng nói. Vui lòng thử lại!';
                if (event.error === 'not-allowed' || event.error === 'permission-denied') {
                    errorMsg = 'Quyền truy cập micro bị từ chối. Vui lòng mở quyền micro trong cài đặt trình duyệt.';
                } else if (event.error === 'no-speech') {
                    errorMsg = 'Không nghe thấy âm thanh. Nhấn vào micro để thử lại.';
                } else if (event.error === 'network') {
                    errorMsg = 'Lỗi kết nối mạng khi nhận diện giọng nói.';
                }

                this.setListeningUIState(false);
                this.setStatusUI('error', errorMsg);
            };

            this.recognition.onend = () => {
                this.isListening = false;
                this.setListeningUIState(false);
            };
        },

        bindEvents: function () {
            // Trigger buttons click (both in header and inventory page)
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.js-voice-search-btn');
                if (btn) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.handleTriggerClick(btn);
                    return;
                }

                // Modal close triggers
                if (e.target.closest('.js-voice-modal-close')) {
                    e.preventDefault();
                    this.closeModal();
                    return;
                }

                // Main mic button inside modal
                if (e.target.closest('.js-voice-main-mic-btn')) {
                    e.preventDefault();
                    if (this.isListening) {
                        this.stopListening(true);
                    } else {
                        this.startListening();
                    }
                    return;
                }

                // Primary Action Button: "Dừng nói & Tìm kiếm" or "Tìm kiếm ngay"
                if (e.target.closest('.js-voice-primary-action-btn')) {
                    e.preventDefault();
                    if (this.isListening) {
                        this.stopListening(true);
                    } else if (this.transcript) {
                        this.executeSearch(this.transcript);
                    } else {
                        this.startListening();
                    }
                    return;
                }

                // Retry Button
                if (e.target.closest('.js-voice-retry-btn')) {
                    e.preventDefault();
                    this.startListening();
                    return;
                }

                // Suggestion Tag Pills
                const tag = e.target.closest('.js-voice-tag');
                if (tag) {
                    e.preventDefault();
                    const query = tag.getAttribute('data-query');
                    if (query) {
                        this.transcript = query;
                        this.updateTranscriptUI(query, false);
                        this.executeSearch(query);
                    }
                }
            });

            // Close on ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modal && this.modal.classList.contains('is-open')) {
                    this.closeModal();
                }
            });
        },

        handleTriggerClick: function (btn) {
            if (!SpeechRecognition) {
                alert('Trình duyệt hiện tại chưa hỗ trợ Web Speech API. Vui lòng sử dụng Google Chrome, Microsoft Edge hoặc Safari!');
                return;
            }

            // Find associated input target
            let targetInput = null;
            const group = btn.closest('.search-input-group, .top-search-wrap, form');
            if (group) {
                targetInput = group.querySelector('input[name="q"], input[type="search"], input[type="text"]');
            }

            this.activeInput = targetInput;
            this.activeTriggerBtn = btn;

            // If already listening, stop and search
            if (this.isListening) {
                this.stopListening(true);
            } else {
                this.openModal();
                this.startListening();
            }
        },

        openModal: function () {
            if (!this.modal) return;
            this.modal.classList.add('is-open');
            this.modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('voice-modal-active');
        },

        closeModal: function () {
            if (!this.modal) return;
            if (this.isListening) {
                this.stopListening(false);
            }
            this.modal.classList.remove('is-open');
            this.modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('voice-modal-active');
        },

        startListening: function () {
            if (!this.recognition) return;

            clearTimeout(this.autoSubmitTimer);
            this.transcript = '';
            this.interimTranscript = '';

            this.updateTranscriptUI('Đang lắng nghe... Hãy nói tên xe bạn tìm!', true);
            this.setStatusUI('listening', 'Đang lắng nghe bạn nói...');

            try {
                this.recognition.start();
            } catch (err) {
                // If already running, stop then restart
                try {
                    this.recognition.stop();
                    setTimeout(() => {
                        this.recognition.start();
                    }, 200);
                } catch (e) {
                    console.error('[VoiceSearch] Start failed:', e);
                }
            }
        },

        stopListening: function (shouldSearch = true) {
            clearTimeout(this.autoSubmitTimer);

            if (this.recognition && this.isListening) {
                try {
                    this.recognition.stop();
                } catch (e) {
                    // Ignore stop errors if already stopped
                }
            }

            this.isListening = false;
            this.setListeningUIState(false);

            if (shouldSearch && this.transcript && this.transcript.trim().length > 0) {
                this.setStatusUI('success', 'Đang xử lý tìm kiếm xe...');
                setTimeout(() => {
                    this.executeSearch(this.transcript);
                }, 400);
            } else {
                this.setStatusUI('idle', 'Nhấn vào micro để nói lại');
            }
        },

        setListeningUIState: function (isListening) {
            // Update modal main mic button
            if (this.mainMicBtn) {
                const idleIcon = this.mainMicBtn.querySelector('.mic-icon-idle');
                const listeningIcon = this.mainMicBtn.querySelector('.mic-icon-listening');

                if (isListening) {
                    this.mainMicBtn.classList.add('is-listening');
                    this.mainMicBtn.classList.remove('is-idle');
                    this.mainMicBtn.setAttribute('title', 'Đang nghe... Bấm để dừng nói');
                    if (idleIcon) idleIcon.style.display = 'none';
                    if (listeningIcon) listeningIcon.style.display = 'inline-block';
                } else {
                    this.mainMicBtn.classList.remove('is-listening');
                    this.mainMicBtn.classList.add('is-idle');
                    this.mainMicBtn.setAttribute('title', 'Bấm vào micro để nói');
                    if (idleIcon) idleIcon.style.display = 'inline-block';
                    if (listeningIcon) listeningIcon.style.display = 'none';
                }
            }

            // Update equalizer visibility
            if (this.equalizer) {
                this.equalizer.style.display = isListening ? 'flex' : 'none';
            }

            // Update modal action buttons
            if (this.primaryActionBtn) {
                const icon = this.primaryActionBtn.querySelector('.voice-action-icon');
                const label = this.primaryActionBtn.querySelector('.voice-action-label');

                if (isListening) {
                    this.primaryActionBtn.className = 'voice-action-btn voice-btn-listening js-voice-primary-action-btn';
                    if (icon) icon.className = 'fa-solid fa-circle-stop me-2 voice-action-icon';
                    if (label) label.textContent = 'Dừng nói & Tìm kiếm';
                    if (this.retryBtn) this.retryBtn.style.display = 'none';
                } else {
                    this.primaryActionBtn.className = 'voice-action-btn voice-btn-primary js-voice-primary-action-btn';
                    if (icon) icon.className = 'fa-solid fa-magnifying-glass me-2 voice-action-icon';
                    if (label) label.textContent = 'Tìm kiếm ngay';
                    if (this.retryBtn) this.retryBtn.style.display = 'inline-flex';
                }
            }

            // Update all inline buttons on the page (Header and Inventory)
            this.allVoiceBtns.forEach((btn) => {
                const idle = btn.querySelector('.voice-mic-idle');
                const listening = btn.querySelector('.voice-mic-listening');

                if (isListening) {
                    btn.classList.add('is-listening');
                    btn.setAttribute('title', 'Đang lắng nghe... Bấm để dừng nói');
                    btn.setAttribute('aria-label', 'Đang lắng nghe... Bấm để dừng nói');
                    if (idle) idle.style.display = 'none';
                    if (listening) listening.style.display = 'inline-block';
                } else {
                    btn.classList.remove('is-listening');
                    btn.setAttribute('title', 'Tìm kiếm bằng giọng nói');
                    btn.setAttribute('aria-label', 'Tìm kiếm bằng giọng nói');
                    if (idle) idle.style.display = 'inline-block';
                    if (listening) listening.style.display = 'none';
                }
            });
        },

        updateTranscriptUI: function (text, isInterim) {
            if (!this.modalTranscript) return;

            if (text) {
                this.modalTranscript.classList.remove('placeholder-active');
                this.modalTranscript.textContent = `"${text}"`;
                if (isInterim) {
                    this.modalTranscript.classList.add('is-interim');
                } else {
                    this.modalTranscript.classList.remove('is-interim');
                }
            } else {
                this.modalTranscript.classList.add('placeholder-active');
                this.modalTranscript.textContent = '"Hãy nói: Mercedes C300, Mazda CX-5, Xe 7 chỗ..."';
            }
        },

        setStatusUI: function (type, text) {
            if (!this.modalStatus) return;

            this.modalStatus.className = `voice-listening-status js-voice-status status-${type}`;
            const textElem = this.modalStatus.querySelector('.status-text');
            if (textElem) {
                textElem.textContent = text;
            }
        },

        /**
         * Clean and normalize raw spoken query for automotive domain
         */
        normalizeQuery: function (rawText) {
            if (!rawText) return '';

            let query = rawText.trim();

            // 1. Remove common conversational filler words at beginning
            const fillerRegex = /^(tìm\s*kiếm|tìm\s*xe|tìm\s*chiếc|tìm\s*con|tìm|cho\s*tôi\s*xem|cho\s*xem|mua\s*xe|kiếm\s*xe|xem\s*xe|có\s*xe\s*nào|chiếc|con\s*xe)\s+/i;
            query = query.replace(fillerRegex, '');

            // 2. Map common Vietnamese spoken slang/brands
            const replacements = [
                { pattern: /\b(mẹc|mer|mec)\b/gi, replacement: 'Mercedes' },
                { pattern: /\b(bim|bim\s*đúp)\b/gi, replacement: 'BMW' },
                { pattern: /\b(pót\s*che|pọt\s*che)\b/gi, replacement: 'Porsche' },
                { pattern: /\b(mác\s*đa)\b/gi, replacement: 'Mazda' },
                { pattern: /\b(lếch\s*xù|lếch\s*xụt)\b/gi, replacement: 'Lexus' },
                { pattern: /\b(huyên\s*đai|huyndai)\b/gi, replacement: 'Hyundai' },
                { pattern: /\b(răng\s*gơ|ran\s*gơ)\b/gi, replacement: 'Ranger' },
                { pattern: /\b(pho\s*tun\s*nơ|pho\s*tu\s*nơ)\b/gi, replacement: 'Fortuner' },
            ];

            replacements.forEach((rule) => {
                query = query.replace(rule.pattern, rule.replacement);
            });

            // Clean multi spaces
            return query.replace(/\s+/g, ' ').trim();
        },

        executeSearch: function (rawQuery) {
            const cleanQuery = this.normalizeQuery(rawQuery);
            if (!cleanQuery) return;

            // Fill active input if present
            if (this.activeInput) {
                this.activeInput.value = cleanQuery;
                this.activeInput.dispatchEvent(new Event('input', { bubbles: true }));
                this.activeInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Check if on inventory page with main filter form
            const mainForm = document.getElementById('inventoryMainFilterForm');
            if (mainForm && (window.location.pathname.includes('/kho-xe') || window.location.pathname.includes('/inventory'))) {
                const formInput = mainForm.querySelector('input[name="q"]');
                if (formInput) {
                    formInput.value = cleanQuery;
                }
                this.closeModal();
                mainForm.submit();
                return;
            }

            // Check if active input has a parent form
            if (this.activeInput && this.activeInput.form) {
                this.closeModal();
                this.activeInput.form.submit();
                return;
            }

            // Default fallback: navigate to inventory page with query
            this.closeModal();
            const fallbackPath = this.inventoryUrl || '/kho-xe';
            const targetUrl = new URL(fallbackPath, window.location.origin);
            targetUrl.searchParams.set('q', cleanQuery);
            window.location.href = targetUrl.toString();
        }
    };

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            VoiceSearch.init();
        });
    } else {
        VoiceSearch.init();
    }

    // Expose on window for testing or future integration (e.g. Chatbot AI trigger)
    window.BoxCarVoiceSearch = VoiceSearch;
})();
