<script setup>
import { ref } from "vue";

import { useForm } from "laravel-precognition-vue"

import AppLayout from "@/Layouts/AppLayout.vue"

defineOptions({
  layout: AppLayout,
})

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
  });

function closeSuccessDialog() {
  showSuccessDialog.value = false;
  form.reset();
  window.location.href = "/";
}
</script>

<template>
    <div class="flex-1 flex items-center justify-center">
      <div class="w-full max-w-md">
        <div class="rounded-2xl shadow-sm ring-1">
          <div class="p-6 sm:p-8">
            <h1 class="text-2xl font-semibold">
              {{ $t('auth.create_account') }}
            </h1>

            <p class="mt-1 text-sm">
              {{ $t('auth.register_subtitle') }}
            </p>

            <form class="mt-6 space-y-5" @submit.prevent="onSubmit" novalidate>
              <!-- Full name -->
              <div class="space-y-1">
                <FloatLabel variant="on">
                  <InputText id="name" v-model="form.name" class="w-full" @change="form.validate('name')"
                    autocomplete="name" />
                  <label for="name">
                    {{ $t('auth.name') }}
                  </label>
                </FloatLabel>

                <small v-if="form.invalid('name')" class="block text-xs">
                  {{ form.errors.name }}
                </small>
              </div>

              <!-- Email -->
              <div class="space-y-1">
                <FloatLabel variant="on">
                  <InputText id="email" v-model="form.email" type="email" inputmode="email" class="w-full"
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
                  <Password id="password" v-model="form.password" class="w-full" input-class="w-full" toggle-mask
                    @change="form.validate('password')" autocomplete="new-password" />
                  <label for="password">
                    {{ $t('auth.password') }}
                  </label>
                </FloatLabel>

                <small v-if="form.invalid('password')" class="block text-xs">
                  {{ form.errors.password }}
                </small>
              </div>

              <!-- Confirm Password -->
              <div class="space-y-1">
                <FloatLabel variant="on">
                  <Password id="password_confirmation" v-model="form.password_confirmation" class="w-full"
                    input-class="w-full" :feedback="false" toggle-mask @change="form.validate('password_confirmation')"
                    autocomplete="new-password" />
                  <label for="password_confirmation">
                    {{ $t('auth.password_confirmation') }}
                  </label>
                </FloatLabel>
              </div>

              <!-- Accept terms -->
              <div class="space-y-3">
                <div class="flex items-center gap-2">
                  <Checkbox input-id="acceptTerms" v-model="form.acceptTerms" @change="form.validate('acceptTerms')"
                    :binary="true" />
                  <label for="acceptTerms" class="text-sm">
                    {{ $t('auth.accept_terms') }}
                  </label>
                </div>

                <small v-if="form.invalid('acceptTerms')" class="block text-xs">
                  {{ form.errors.acceptTerms }}
                </small>
              </div>

              <Button type="submit" :label="$t('auth.create_account')" class="w-full" :disabled="form.processing" />
            </form>
          </div>
        </div>
      </div>

      <Dialog v-model:visible="showSuccessDialog" modal :closable="true" :draggable="false"
        :header="$t('auth.register_dialog_title')" class="w-[92vw] max-w-md" @hide="closeSuccessDialog">
        <p>
          {{ $t('auth.register_dialog_message') }}
        </p>

        <template #footer>
          <div class="flex w-full justify-end gap-2">
            <Button :label="$t('auth.close')" severity="secondary" @click="closeSuccessDialog" />
          </div>
        </template>
      </Dialog>
    </div>
</template>
