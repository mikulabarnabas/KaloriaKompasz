<script setup>
import { ref } from "vue";

import { useForm } from "laravel-precognition-vue"

import AppLayout from "@/Layouts/AppLayout.vue"

defineOptions({
  layout: AppLayout,
})

const showSuccessDialog = ref(false);

const form = useForm('post', '/login', {
  email: "",
  password: "",
  remeber_token: false,
});

const onSubmit = () => form.submit()
  .then(response => {
    form.reset();
    showSuccessDialog.value = true;
  });

function closeSuccessDialog() {
  showSuccessDialog.value = false;
  form.reset();
  window.location.href = "/";
}
</script>

<template>
    <div class="w-full max-w-md">
      <div class="rounded-2xl shadow-sm ring-1">
        <div class="p-6 sm:p-8">
          <h1 class="text-2xl font-semibold">
            {{ $t('auth.sign_in') }}
          </h1>

          <p class="mt-1 text-sm">
            {{ $t('auth.login_subtitle') }}
          </p>

          <form class="mt-6 space-y-5" @submit.prevent="onSubmit" novalidate>
            <!-- Email -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText id="email" v-model="form.email" type="text" inputmode="email" class="w-full"
                  @change="form.validate('email')" autocomplete="email" />
                <label for="email">
                  {{ $t('auth.email') }}
                </label>
              </FloatLabel>

              <small v-if="form.invalid('email')" class="block text-xs">
                {{ form.errors.email }}
              </small>
            </div>

            <!-- Password -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <Password id="password" :feedback="false" v-model="form.password" class="w-full" input-class="w-full"
                  toggle-mask @change="form.validate('password')" autocomplete="new-password" />
                <label for="password">
                  {{ $t('auth.password') }}
                </label>
              </FloatLabel>

              <small v-if="form.invalid('password')" class="block text-xs">
                {{ form.errors.password }}
              </small>
            </div>

            <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-2">
                <Checkbox input-id="remember" v-model="form.rememberme" :binary="true" />
                <label for="remember" class="text-sm">
                  {{ $t('auth.remember_me') }}
                </label>
              </div>

              <a href="#" class="text-sm font-medium">
                {{ $t('auth.forgot_password') }}
              </a>
            </div>

            <Button type="submit" :label="$t('auth.sign_in')" class="w-full" />

            <p class="text-center text-sm">
              {{ $t('auth.no_account') }}
              <a href="/register" class="font-medium">
                {{ $t('auth.registration') }}
              </a>
            </p>
          </form>
        </div>
      </div>
    </div>

    <Dialog v-model:visible="showSuccessDialog" modal :closable="true" :draggable="false"
      :header="$t('auth.login_dialog_title')" class="w-[92vw] max-w-md" @hide="closeSuccessDialog">

      <template #footer>
        <div class="flex w-full justify-end gap-2">
          <Button :label="$t('auth.close')" severity="secondary" @click="closeSuccessDialog" />
        </div>
      </template>
    </Dialog>
</template>


<style scoped></style>