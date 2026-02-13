<script setup>
import { ref } from "vue"
import AppLayout from "@/Layouts/AppLayout.vue"
import LoginForm from "@/Components/loginForm.vue"
import RegisterForm from "@/Components/registerForm.vue"

defineOptions({ layout: AppLayout })

const tab = ref("login")
</script>

<template>
  <div class="min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md">

      <!-- Card -->
      <div class="w-full max-w-md rounded-3xl border
                backdrop-blur-xl p-8 shadow-2xl">
        <div class="flex bg-white/5 rounded-xl p-1 mb-8">
          <button @click="tab = 'login'" :class="[
            'flex-1 py-2 rounded-lg text-black font-semibold transition-colors duration-200',
            tab === 'login' ? 'bg-primary' : 'bg-transparent hover:bg-white/10'
          ]">
            {{ $t('auth.sign_in') }}
          </button>

          <button @click="tab = 'register'" :class="[
            'flex-1 py-2 rounded-lg text-black font-semibold transition-colors duration-200',
            tab === 'register' ? 'bg-primary' : 'bg-transparent hover:bg-white/10'
          ]">
            {{ $t('auth.registration') }}
          </button>
        </div>

        <LoginForm v-if="tab === 'login'" />
        <RegisterForm v-else />

      </div>
    </div>
  </div>
</template>
