<script setup>
import { ref } from 'vue';
import Button from "@/Components/button.vue"
import IconCompass from '@/Components/iconCompass.vue'

const isSourceModalOpen = ref(false);
</script>

<template>
  <footer class="bg-background-dark border-t border-neutral-border/30 pt-16 pb-8 px-6 font-sans">
    <div class="max-w-7xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

        <div class="space-y-4">
          <div class="flex items-center gap-2">
            <IconCompass></IconCompass>
            <span class="text-2xl font-black tracking-tighter uppercase text-main-text">
              Kalória<span class="text-primary">Kompasz</span>
            </span>
          </div>
          <p
            class="text-secondary-text text-xs leading-relaxed max-w-xs font-medium uppercase tracking-wider opacity-70">
            {{ $t('footer.description') }}
          </p>
        </div>

        <div class="space-y-6">
          <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.3em]">{{ $t('footer.docs_title') }}</h3>
          <ul class="space-y-3">
            <li>
              <a href="/documents/ASZF.pdf" target="_blank" rel="noopener noreferrer"
                class="text-main-text/60 hover:text-primary text-xs font-bold uppercase tracking-widest transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">description</span>
                {{ $t('footer.aszf') }}
              </a>
            </li>
            <li>
              <a href="/documents/adatkez.pdf" target="_blank" rel="noopener noreferrer"
                class="text-main-text/60 hover:text-primary text-xs font-bold uppercase tracking-widest transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">shield</span>
                {{ $t('footer.privacy') }}
              </a>
            </li>
            <li>
              <button @click="isSourceModalOpen = true"
                class="text-main-text/60 hover:text-primary text-xs font-bold uppercase tracking-widest transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">menu_book</span>
                {{ $t('footer.sources') }}
              </button>
            </li>
          </ul>
        </div>

        <div class="space-y-6">
          <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.3em]">{{ $t('footer.operator_title') }}</h3>
          <div class="text-secondary-text text-[11px] font-bold uppercase tracking-widest space-y-2">
            <p class="text-main-text/80">{{ 'Mikula Barnabás & Bóta Gergely' }}</p>
            <p>{{ $t('footer.storage_provider') }}: {{ 'Rackhost Zrt.' }}</p>
          </div>
        </div>

        <div class="space-y-6">
          <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.3em]">{{ $t('footer.contact_title') }}</h3>
          <div class="space-y-3">
            <a href="mailto:mikula.barnabas1234@gmail.com"
              class="block text-main-text/60 hover:text-primary text-xs font-bold transition-colors">
              mikula.barnabas1234@gmail.com
            </a>
            <p class="text-main-text/40 text-[10px] font-black uppercase tracking-widest italic">
              {{ $t('footer.exam_project') }} (2026)
            </p>
          </div>
        </div>
      </div>

      <div class="pt-8 border-t border-neutral-border/10 flex flex-col md:row justify-between items-center gap-4">
        <p class="text-[9px] font-black text-secondary-text uppercase tracking-[0.4em] opacity-40">
          &copy; 2026 KalóriaKompasz. {{ $t('footer.all_rights_reserved') }}
        </p>
        <div class="flex gap-6 opacity-30">
          <span class="material-symbols-outlined text-main-text text-lg">ecg_heart</span>
          <span class="material-symbols-outlined text-main-text text-lg">fitness_center</span>
          <span class="material-symbols-outlined text-main-text text-lg">nutrition</span>
        </div>
      </div>
    </div>

    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
      enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100"
      leave-to-class="opacity-0">

      <div v-if="isSourceModalOpen"
        class="fixed inset-0 z-[110] overflow-y-auto flex items-center justify-center px-4 py-6">

        <div class="fixed inset-0 bg-background-dark/80 backdrop-blur-sm transition-opacity"
          @click="isSourceModalOpen = false"></div>

        <div
          class="relative w-full max-w-lg bg-background-dark rounded-4xl shadow-2xl border border-neutral-border p-8 transform transition-all">

          <button @click="isSourceModalOpen = false"
            class="absolute top-6 right-6 text-secondary-text hover:text-primary transition-colors">
            <span class="material-symbols-outlined">close</span>
          </button>

          <div class="mb-8">
            <h2 class="text-xs font-black text-primary uppercase tracking-[0.3em] mb-1">
              {{ $t('footer.modal_subtitle') }}
            </h2>
            <h3 class="text-xl font-black text-main-text uppercase tracking-tight">
              {{ $t('footer.modal_title') }}
            </h3>
          </div>

          <div class="space-y-6">
            <div
              class="bg-neutral-dark/20 p-5 rounded-2xl border border-neutral-border/50 group hover:border-primary/30 transition-colors">
              <h4
                class="text-[10px] font-black text-secondary-text uppercase tracking-widest mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm text-primary">nutrition</span> {{ $t('footer.food_data') }}
              </h4>
              <ul class="space-y-2 text-xs text-main-text/80 font-medium">
                <li class="flex items-start gap-2">
                  <span class="text-primary">•</span>
                  <a href="https://hu.openfoodfacts.org/" target="_blank"
                    class="hover:text-primary transition-colors underline decoration-primary/30">Open Food Facts</a>
                </li>
                <li class="flex items-start gap-2">
                  <span class="text-primary">•</span>
                  <a href="https://www.kaggle.com/datasets/utsavdey1410/food-nutrition-dataset" target="_blank"
                    class="hover:text-primary transition-colors underline decoration-primary/30 text-[11px]">Food
                    Nutrition Dataset (Kaggle)</a>
                </li>
              </ul>
            </div>

            <div
              class="bg-neutral-dark/20 p-5 rounded-2xl border border-neutral-border/50 group hover:border-primary/30 transition-colors">
              <h4
                class="text-[10px] font-black text-secondary-text uppercase tracking-widest mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm text-primary">fitness_center</span> {{ $t('footer.exercise_data') }}
              </h4>
              <ul class="space-y-2 text-xs text-main-text/80 font-medium">
                <li class="flex items-start gap-2">
                  <span class="text-primary">•</span>
                  <a href="https://www.kaggle.com/datasets/aadhavvignesh/calories-burned-during-exercise-and-activities" target="_blank"
                    class="hover:text-primary transition-colors underline decoration-primary/30 text-[11px]">
                    Calories Burned During Exercise (Kaggle)</a>
                </li>
              </ul>
            </div>


            <div
              class="bg-neutral-dark/20 p-5 rounded-2xl border border-neutral-border/50 group hover:border-primary/30 transition-colors">
              <h4
                class="text-[10px] font-black text-secondary-text uppercase tracking-widest mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm text-primary">science</span> {{ $t('footer.scientific_background') }}
              </h4>
              <p class="text-sm text-main-text/80 leading-relaxed" v-html="$t('footer.mifflin_description', { 
                link: '<a href=\'https://reference.medscape.com/calculator/846/mifflin-st-jeor-equation\' target=\'_blank\' class=\'hover:text-primary underline decoration-primary/30\'><strong>Mifflin-St Jeor</strong></a>' 
              })"></p>
            </div>
          </div>

          <div class="mt-8">
            <Button icon="" @click="isSourceModalOpen = false" :label="$t('footer.close_button')" class="w-full" />
          </div>
        </div>
      </div>
    </Transition>
  </footer>
</template>