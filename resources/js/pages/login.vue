<script setup>
import { ref } from "vue"
import AppLayout from "@/Layouts/AppLayout.vue"
import LoginForm from "@/Components/loginForm.vue"
import RegisterForm from "@/Components/registerForm.vue"
import SuccessDialog from "@/Components/successDialog.vue";
import { router } from "@inertiajs/vue3";

const loginSuccess = ref(false);
const registerSuccess = ref(false);

const handleClose = () => {
  loginSuccess.value = false;
  router.visit("/stats")
};

defineOptions({ layout: AppLayout })
const tab = ref("login")
</script>

<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4 transition-colors duration-500">
    <div class="w-full max-w-md">
      <div class="rounded-[2.5rem] border border-black/5 dark:border-white/10 
                  bg-white/70 dark:bg-white/5 backdrop-blur-2xl p-8 
                  shadow-[0_20px_50px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)]">

        <div class="relative flex bg-black/5 dark:bg-white/10 rounded-xl p-1.5 mb-8">
          <div class="absolute inset-y-1.5 transition-all duration-300 ease-out bg-primary rounded-xl shadow-sm"
            :style="{ left: tab === 'login' ? '6px' : 'calc(50% + 2px)', width: 'calc(50% - 8px)' }">
          </div>

          <button @click="tab = 'login'"
            class="relative z-10 flex-1 py-2.5 text-sm font-bold transition-colors duration-300"
            :class="tab === 'login' ? 'text-black' : 'text-black/40 dark:text-white/40 hover:text-black/60 dark:hover:text-white/60'">
            {{ $t('auth.sign_in') }}
          </button>

          <button @click="tab = 'register'"
            class="relative z-10 flex-1 py-2.5 text-sm font-bold transition-colors duration-300"
            :class="tab === 'register' ? 'text-black' : 'text-black/40 dark:text-white/40 hover:text-black/60 dark:hover:text-white/60'">
            {{ $t('auth.registration') }}
          </button>
        </div>

        <Transition name="fade-slide" mode="out-in">
          <LoginForm v-if="tab === 'login'" @success="loginSuccess = $event" />
          <RegisterForm v-else @success="registerSuccess = $event"/>
        </Transition>
      </div>
    </div>
  </div>

  <SuccessDialog :show="loginSuccess" :title="$t('auth.login_dialog_title')"
    :message="$t('auth.login_dialog_message')" :button-text="$t('auth.close')" @close="handleClose" />

  <SuccessDialog :show="registerSuccess" :title="$t('auth.register_dialog_title')"
    :message="$t('auth.register_dialog_message')" :button-text="$t('auth.close')" @close="handleClose" />
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>