{{-- Google reCAPTCHA v2 Component --}}
<div class="g-recaptcha-wrapper mb-4">
    <div class="g-recaptcha" 
         data-sitekey="{{ config('captcha.sitekey') }}"
         data-theme="{{ $theme ?? 'light' }}"
         data-size="{{ $size ?? 'normal' }}">
    </div>
    @error('g-recaptcha-response')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

@once
    @push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@endonce
