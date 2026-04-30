{{-- 
    Chatbot Widget Component — MeseriasiOnline
    Folosit în: resources/views/layouts/app.blade.php
    Tehnologie: Alpine.js + Tailwind CSS + Fetch API
--}}

<div
    x-data="chatbotWidget()"
    x-init="init()"
    class="fixed bottom-6 right-6 z-[9000] flex flex-col items-end"
    style="font-family: 'Nunito', sans-serif;"
>
    {{-- ─── Fereastra de chat ─── --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="mb-4 w-[340px] sm:w-[380px] bg-white rounded-2xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden"
        style="max-height: 520px; min-height: 400px;"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold leading-none">Asistent Omul Potrivit</p>
                    <p class="text-xs text-red-100 mt-0.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span>
                        Online acum
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button
                    @click="resetChat()"
                    title="Conversație nouă"
                    class="text-white/70 hover:text-white transition p-1 rounded"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
                <button
                    @click="isOpen = false"
                    title="Închide"
                    class="text-white/70 hover:text-white transition p-1 rounded"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Zona de mesaje --}}
        <div
            x-ref="messagesContainer"
            class="flex-1 overflow-y-auto px-4 py-3 space-y-3 bg-gray-50"
            style="min-height: 260px; max-height: 320px;"
        >
            {{-- Mesajul de bun venit --}}
            <template x-if="messages.length === 0">
                <div class="flex flex-col gap-3">
                    <div class="flex gap-2">
                        <div class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div class="bg-white rounded-2xl rounded-tl-sm px-3 py-2 shadow-sm max-w-[85%]">
                            <p class="text-sm text-gray-800">Bună! 👋 Sunt asistentul platformei <strong>Omul Potrivit</strong>.</p>
                            <p class="text-sm text-gray-800 mt-1">Cum te pot ajuta azi?</p>
                        </div>
                    </div>
                    {{-- Quick replies --}}
                    <div class="flex flex-wrap gap-2 pl-9">
                        <template x-for="suggestion in quickReplies" :key="suggestion">
                            <button
                                @click="sendMessage(suggestion)"
                                class="text-xs bg-white border border-red-200 text-red-700 hover:bg-red-50 hover:border-red-400 rounded-full px-3 py-1.5 transition font-medium shadow-sm"
                                x-text="suggestion"
                            ></button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Mesajele conversației --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex gap-2'">
                    {{-- Avatar asistent --}}
                    <div x-show="msg.role === 'assistant'" class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="max-w-[85%] flex flex-col gap-1.5">
                        {{-- Bula mesaj --}}
                        <div
                            :class="msg.role === 'user'
                                ? 'bg-red-600 text-white rounded-2xl rounded-tr-sm px-3 py-2'
                                : 'bg-white text-gray-800 rounded-2xl rounded-tl-sm px-3 py-2 shadow-sm'"
                        >
                            <p class="text-sm leading-relaxed whitespace-pre-wrap" x-text="msg.content"></p>
                        </div>
                        {{-- Butoane CTA (acțiuni) --}}
                        <template x-if="msg.actions && msg.actions.length > 0">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="action in msg.actions" :key="action.url">
                                    <a
                                        :href="action.url"
                                        @click="trackConversion(action.url)"
                                        :class="action.type === 'primary'
                                            ? 'bg-red-600 hover:bg-red-700 text-white'
                                            : 'bg-white border border-gray-200 hover:bg-gray-50 text-gray-700'"
                                        class="text-xs rounded-lg px-3 py-1.5 font-semibold transition shadow-sm inline-block"
                                        x-text="action.label"
                                    ></a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="isTyping" class="flex gap-2">
                <div class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                    <div class="flex gap-1 items-center">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input zona --}}
        <div class="px-3 py-3 border-t border-gray-100 bg-white">
            <form @submit.prevent="sendUserMessage()" class="flex gap-2">
                <input
                    x-ref="messageInput"
                    x-model="currentMessage"
                    type="text"
                    placeholder="Scrie un mesaj..."
                    maxlength="500"
                    :disabled="isTyping"
                    class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400 disabled:opacity-50 transition"
                    autocomplete="off"
                />
                <button
                    type="submit"
                    :disabled="!currentMessage.trim() || isTyping"
                    class="bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl px-3 py-2 transition flex items-center justify-center"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
            <p class="text-center text-xs text-gray-400 mt-2">Asistent AI · MeseriasiOnline.ro</p>
        </div>
    </div>

    {{-- ─── Butonul flotant (toggle) ─── --}}
    <button
        @click="toggleChat()"
        class="w-14 h-14 bg-gradient-to-br from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center relative"
        :title="isOpen ? 'Închide chat' : 'Deschide chat'"
    >
        {{-- Iconița chat (când e închis) --}}
        <svg
            x-show="!isOpen"
            x-transition:enter="transition duration-150"
            x-transition:enter-start="opacity-0 scale-75"
            x-transition:enter-end="opacity-100 scale-100"
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        {{-- Iconița X (când e deschis) --}}
        <svg
            x-show="isOpen"
            x-transition:enter="transition duration-150"
            x-transition:enter-start="opacity-0 scale-75"
            x-transition:enter-end="opacity-100 scale-100"
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{-- Badge notificare (prima dată) --}}
        <span
            x-show="showBadge"
            class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse"
        >1</span>
    </button>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatbotWidget', () => ({
        isOpen: false,
        isTyping: false,
        currentMessage: '',
        messages: [],
        showBadge: true,
        quickReplies: [
            'Vreau să mă înscriu ca meseriaș',
            'Am nevoie de un meseriaș',
            'Cum funcționează platforma?',
            'Care sunt prețurile?',
        ],

        init() {
            // Ascunde badge-ul după 5 secunde
            setTimeout(() => { this.showBadge = false; }, 5000);
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            this.showBadge = false;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.$refs.messageInput?.focus();
                });
            }
        },

        async sendUserMessage() {
            const msg = this.currentMessage.trim();
            if (!msg || this.isTyping) return;

            this.sendMessage(msg);
        },

        async sendMessage(text) {
            if (this.isTyping) return;

            this.currentMessage = '';
            this.isOpen = true;
            this.showBadge = false;

            // Adaugă mesajul utilizatorului
            this.messages.push({
                role: 'user',
                content: text,
                actions: [],
            });

            this.isTyping = true;
            this.scrollToBottom();

            try {
                const response = await fetch('/api/chatbot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: text }),
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.messages.push({
                        role: 'assistant',
                        content: data.message,
                        actions: data.actions ?? [],
                    });
                } else {
                    this.messages.push({
                        role: 'assistant',
                        content: data.message ?? 'A apărut o eroare. Încearcă din nou.',
                        actions: [],
                    });
                }
            } catch (err) {
                this.messages.push({
                    role: 'assistant',
                    content: 'Nu mă pot conecta momentan. Verifică conexiunea și încearcă din nou.',
                    actions: [],
                });
            }

            this.isTyping = false;
            this.$nextTick(() => {
                this.scrollToBottom();
                this.$refs.messageInput?.focus();
            });
        },

        async resetChat() {
            this.messages = [];
            try {
                await fetch('/api/chatbot/reset', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'Accept': 'application/json',
                    },
                });
            } catch (e) {
                // Ignore errors on reset
            }
        },

        async trackConversion(url) {
            try {
                await fetch('/api/chatbot/convert', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ url }),
                });
            } catch (e) {
                // Non-blocking — don't interrupt navigation
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },
    }));
});
</script>
