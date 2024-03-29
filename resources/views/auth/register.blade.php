<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <img src="{{asset('images/favicon-min.png')}}">
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            <h1 class="text-center">Регистрация</h1>
            @csrf

            <div>
                <x-jet-label for="name" value="{{ __('ФИО') }}" />
                <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>
            
            <div class="mt-4">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>
            <div class="row">
                <span><small>Почтовый ящик обязательно должен быть в почтовом домене @psuti.ru Пароль от аккаунта будет выслан на данную почту.</small></span>
            </div>
            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-jet-label for="terms">
                        <div class="flex items-center">
                            <x-jet-checkbox name="terms_1" id="terms_1"/>

                            <div class="ml-2">
                                {!! __('Я принимаю условия :privacy_agreement', [
                                        'privacy_agreement' => '<a target="_blank" href="/files/privacy_agreement.pdf" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('соглашения о конфиденциальности').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-jet-label>
                </div>
                <div class="mt-4">
                    <x-jet-label for="terms">
                        <div class="flex items-center">
                            <x-jet-checkbox name="terms_2" id="terms_2"/>

                            <div class="ml-2">
                                {!! __('Я принимаю условия :personal_data_processing_agreement', [
                                        'personal_data_processing_agreement' => '<a target="_blank" href="/files/personal_data_processing_agreement.pdf" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('соглашения на обработку персольных данных').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-jet-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Уже зарегистрированы?') }}
                </a>

                <x-jet-button class="ml-4">
                    {{ __('Регистрация') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
