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

const form = useForm('post', '/register', {
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  acceptTerms: false
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
  <div class="min-h-screen w-full bg-slate-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="p-6 sm:p-8">
          <h1 class="text-2xl font-semibold text-slate-900">Create account</h1>
          <p class="mt-1 text-sm text-slate-600">
            Fill in your details to get started.
          </p>

          <form class="mt-6 space-y-5" @submit.prevent="onSubmit" novalidate>
            <!-- Full name -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText id="name" v-model="form.name" class="w-full" @change="form.validate('name')"
                  autocomplete="name" />
                <label for="name">Full name</label>
              </FloatLabel>

              <small v-if="form.invalid('name')" class="block text-xs text-red-600">
                {{ form.errors.name }}
              </small>
            </div>

            <!-- Email -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText id="email" v-model="form.email" type="email" inputmode="email" class="w-full"
                  @change="form.validate('email')" autocomplete="email" />
                <label for="email">Email</label>
              </FloatLabel>

              <small v-if="form.invalid('email')" class="block text-xs text-red-600">
                {{ form.errors.email }}
              </small>
            </div>

            <!-- Password -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <Password id="password" v-model="form.password" class="w-full" input-class="w-full" toggle-mask
                  @change="form.validate('password')" autocomplete="new-password" />
                <label for="password">Password</label>
              </FloatLabel>

              <small v-if="form.invalid('password')" class="block text-xs text-red-600" ">
                {{ form.errors.password }}
              </small>
            </div>

            <!-- Confirm Password -->
            <div class=" space-y-1">
                <FloatLabel variant="on">
                  <Password id="password_confirmation" v-model="form.password_confirmation" class="w-full"
                    input-class="w-full" :feedback="false" toggle-mask @change="form.validate('password_confirmation')"
                    autocomplete="new-password" />
                  <label for="password_confirmation">Confirm password</label>
                </FloatLabel>
            </div>

            <div class="space-y-3">
              <div class="flex items-center gap-2">
                <Checkbox input-id="acceptTerms" v-model="form.acceptTerms" @change="form.validate('acceptTerms')"
                  :binary="true" />
                <label for="acceptTerms" class="text-sm text-slate-700">
                  I agree to the Terms & Privacy Policy
                </label>
              </div>

              <small v-if="form.invalid('acceptTerms')" class="block text-xs text-red-600"
                :class="form.errors.acceptTerms">
                {{ form.errors.acceptTerms }}
              </small>

            </div>

            <Button type="submit" label="Create account" class="w-full" :disabled="form.processing" />
          </form>
        </div>
      </div>

      <p class="mt-4 text-center text-xs text-slate-500">
        By continuing you agree to our Terms & Privacy Policy.
      </p>
    </div>

    <Dialog v-model:visible="showSuccessDialog" modal :closable="true" :draggable="false"
      header="Registration successful" class="w-[92vw] max-w-md" @hide="closeSuccessDialog">
      <p class="text-slate-700">
        Your account has been created successfully. You can now sign in.
      </p>

      <template #footer>
        <div class="flex w-full justify-end gap-2">
          <Button label="Close" severity="secondary" @click="closeSuccessDialog" />
        </div>
      </template>
    </Dialog>
  </div>
</template>