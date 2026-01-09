<div class="min-h-screen flex">
    <!-- Left Column - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-green-600">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <!-- Logo and Title -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-600 rounded-full mb-4">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">WhatsApp Masivo</h1>
                    <p class="text-gray-600 mt-2">Inicia sesión para continuar</p>
                </div>

                <!-- Error Message -->
                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Login Form -->
                <form wire:submit.prevent="login" class="space-y-6">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Correo Electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input
                                type="email"
                                id="email"
                                wire:model="email"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                placeholder="ejemplo@correo.com"
                                required
                            >
                        </div>
                        @error('email') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input
                                type="password"
                                id="password"
                                wire:model="password"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                placeholder="••••••••"
                                required
                            >
                        </div>
                        @error('password') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105 focus:ring-4 focus:ring-green-300"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Iniciar Sesión</span>
                        <span wire:loading>Iniciando...</span>
                    </button>
                </form>

                <!-- Demo Credentials -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-600 text-center font-semibold mb-2">Credenciales de Demo:</p>
                    <div class="text-xs text-gray-700 space-y-1">
                        <p><strong>Email:</strong> mensaje@demo.com</p>
                        <p><strong>Password:</strong> admin123</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Image -->
    <div class="hidden lg:flex lg:w-1/2 bg-green-700 items-center justify-center p-12">
        <div class="max-w-lg text-center">
            <img
                src="https://bcdn.askleo.com/wp-content/uploads/2020/02/mobile.jpg"
                alt="Personas enviando mensajes"
                class="w-full h-auto rounded-2xl shadow-2xl mb-8"
                onerror="this.src='https://via.placeholder.com/600x400/10b981/ffffff?text=WhatsApp+Masivo'"
            >
            <h2 class="text-4xl font-bold text-white mb-4">
                Envía mensajes masivos de forma eficiente
            </h2>
            <p class="text-green-100 text-lg">
                Gestiona tus campañas de WhatsApp, envía mensajes a miles de contactos y obtén métricas detalladas en tiempo real.
            </p>
        </div>
    </div>
</div>
