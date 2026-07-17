<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <style>
            .pw-input-wrap { position: relative; }
            .pw-input-wrap input { padding-right: 44px; }
            .pw-toggle {
                position: absolute; right: 2px; top: 50%; transform: translateY(-50%);
                background: none; border: none; cursor: pointer; padding: 8px 12px;
                color: #9ca3af; font-size: 18px; display: flex; align-items: center;
                transition: color 0.2s; line-height: 1;
            }
            .pw-toggle:hover { color: #6b7280; }
        </style>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <div class="pw-input-wrap mt-1">
                <x-text-input id="password" class="block w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <button type="button" class="pw-toggle" onclick="togglePw('password', this)" tabindex="-1">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>

            <x-input-error :messages="\->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
function togglePw(fieldId, btn) {
    var field = document.getElementById(fieldId);
    var icon = btn.querySelector('i');
    if (!field) return;
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    } else {
        field.type = 'password';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    }
}
</script>
