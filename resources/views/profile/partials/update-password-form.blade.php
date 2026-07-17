<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

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

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <div class="pw-input-wrap mt-1">
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full" autocomplete="current-password" />
                <button type="button" class="pw-toggle" onclick="togglePw('update_password_current_password', this)" tabindex="-1">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <x-input-error :messages="\->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <div class="pw-input-wrap mt-1">
                <x-text-input id="update_password_password" name="password" type="password" class="block w-full" autocomplete="new-password" />
                <button type="button" class="pw-toggle" onclick="togglePw('update_password_password', this)" tabindex="-1">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <x-input-error :messages="\->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <div class="pw-input-wrap mt-1">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" />
                <button type="button" class="pw-toggle" onclick="togglePw('update_password_password_confirmation', this)" tabindex="-1">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <x-input-error :messages="\->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

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
