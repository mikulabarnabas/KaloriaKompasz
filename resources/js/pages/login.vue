<script setup>
import { ref } from "vue";

import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Checkbox from "primevue/checkbox";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import { useForm } from "laravel-precognition-vue"

const showSuccessDialog = ref(false);

const form = useForm('post', '/login', {
  email: "",
  password: "",
  remeber_token: true,
});

const onSubmit = () => form.submit()
  .then(response => {
    form.reset();
    showSuccessDialog.value = true;
  })
  .catch(error => {

  });

function closeSuccessDialog() {
  showSuccessDialog.value = false;
  form.reset();
  window.location.href = "/";
}
</script>

<template>
  <div class="min-h-screen w-full flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="rounded-2xl shadow-sm ring-1">
        <div class="p-6 sm:p-8">
          <h1 class="text-2xl font-semibold">Sign in</h1>
          <p class="mt-1 text-sm">
            Use your email and password to continue.
          </p>

          <form class="mt-6 space-y-5" @submit.prevent="onSubmit" novalidate>
            <!-- Email -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText id="email" v-model="form.email" type="email" inputmode="email" class="w-full"
                  @change="form.validate('email')" autocomplete="email" />
                <label for="email">Email</label>
              </FloatLabel>

              <small v-if="form.invalid('email')" class="block text-xs">
                {{ form.errors.email }}
              </small>
            </div>

            <!-- Password -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <Password id="password" :feedback="false" v-model="form.password" class="w-full" input-class="w-full" toggle-mask
                  @change="form.validate('password')" autocomplete="new-password" />
                <label for="password">Password</label>
              </FloatLabel>

              <small v-if="form.invalid('password')" class="block text-xs" ">
                {{ form.errors.password }}
              </small>
            </div>

            <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-2">
                <Checkbox input-id="remember" v-model="form.rememberme" :binary="true" />
                <label for="rememberme" class="text-sm">Remember me</label>
              </div>

              <a href="#" class="text-sm font-medium">
                Forgot password?
              </a>
            </div>

            <Button type="submit" label="Sign in" class="w-full" :loading="loading" />

            <p class="text-center text-sm">
              Don't have an account?
              <a href="/register" class="font-medium">
                Create one
              </a>
            </p>
          </form>
        </div>
      </div>

      <p class="mt-4 text-center text-xs">
        By continuing you agree to our Terms & Privacy Policy.
      </p>
    </div>

    <Dialog v-model:visible="showSuccessDialog" modal :closable="true" :draggable="false"
      header="Login successful" class="w-[92vw] max-w-md" @hide="closeSuccessDialog">
      <p>
        You signed in.
      </p>

      <template #footer>
        <div class="flex w-full justify-end gap-2">
          <Button label="Close" severity="secondary" @click="closeSuccessDialog" />
        </div>
      </template>
    </Dialog>
  </div>
</template>

<style scoped></style>