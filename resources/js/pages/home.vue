<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import Accordion from "@/Components/accordion.vue"

const faqItems = [
  {
    question: "Miben más ez, mint a többi app?",
    answer: "Személyesebbek vagyunk. Itt nem egy multi algoritmusai mögé bújsz, hanem egy olyan eszközt kapsz, amit mi is nap mint nap használunk az edzéseinkhez."
  },
  {
    question: "Szinkronizálhatom az órámat?",
    answer: "Igen, folyamatosan dolgozunk az Apple Health és Google Fit integrációkon, hogy az adatok maguktól áramoljanak."
  },
  {
    question: "Ingyenes marad az alkalmazás?",
    answer: "Az alap funkciók (kalória és edzésnapló) örökre ingyenesek maradnak, mert hiszünk abban, hogy az egészség nem lehet pénzkérdés."
  }
];

</script>

<template>
  <AppLayout>
    <div class="relative flex min-h-screen flex-col overflow-x-hidden">
      <main class="grow">
        <section class="relative overflow-hidden px-6 py-20 lg:py-32">
          <div class="mx-auto max-w-7xl">
            <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
              <div class="flex flex-col gap-8">
                <h1 class="text-5xl font-black leading-[1.1] text-white md:text-7xl">
                  Több mint egy <span class="text-primary">Kalória</span> & <span class="text-primary">Edzés</span>
                  napló
                </h1>
                <p class="max-w-xl text-lg leading-relaxed text-white/60">
                  Nem csak egy alkalmazást építünk, hanem a jövőd alapkövét. Pontos követés és átlátható adatok,
                  sallangok nélkül.
                </p>
                <div class="flex flex-wrap gap-4">
                  <button
                    class="flex items-center gap-2 rounded-xl bg-primary px-8 py-4 text-lg font-bold text-background-dark hover:shadow-[0_0_20px_rgba(13,242,89,0.4)] transition-all">
                    Vágj bele most
                    <span class="material-symbols-outlined">arrow_forward</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="bg-neutral-dark/30 py-24 px-6">
          <div class="mx-auto max-w-7xl">
            <div class="mb-16 flex flex-col items-center text-center">
              <h2 class="text-4xl font-black text-white md:text-5xl">A három pillér</h2>
              <div class="mt-4 h-1.5 w-24 rounded-full bg-primary"></div>
              <p class="mt-6 max-w-2xl text-lg text-white/60">
                Olyan ökoszisztémát alkottunk, amiben minden megvan, amire a fejlődésedhez szükséged lehet – a konyhától
                a konditeremig.
              </p>
            </div>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
              <div
                class="group relative flex flex-col gap-6 rounded-3xl border border-neutral-border bg-neutral-dark p-8 hover:border-primary/50 transition-all">
                <div
                  class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-background-dark transition-all">
                  <span class="material-symbols-outlined text-3xl font-bold">restaurant</span>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-white">Étrend napló</h3>
                  <p class="mt-3 text-white/60 leading-relaxed">
                    Gyors és egyszerű étkezésrögzítés. Pontos makrotápanyag lebontás, hogy pontosan tudd, mit viszel be
                    a szervezetedbe.
                  </p>
                </div>
              </div>
              <div
                class="group relative flex flex-col gap-6 rounded-3xl border border-neutral-border bg-neutral-dark p-8 hover:border-primary/50 transition-all">
                <div
                  class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-background-dark transition-all">
                  <span class="material-symbols-outlined text-3xl font-bold">fitness_center</span>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-white">Edzésterv</h3>
                  <p class="mt-3 text-white/60 leading-relaxed">
                    Saját rutinok összeállítása pillanatok alatt. Kövesd a fejlődésed sorozatról sorozatra, és törd át a
                    korlátaidat.
                  </p>
                </div>
              </div>
              <div
                class="group relative flex flex-col gap-6 rounded-3xl border border-neutral-border bg-neutral-dark p-8 hover:border-primary/50 transition-all">
                <div
                  class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-background-dark transition-all">
                  <span class="material-symbols-outlined text-3xl font-bold">insights</span>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-white">Statisztikák</h3>
                  <p class="mt-3 text-white/60 leading-relaxed">
                    Látványos grafikonok a haladásodról. Lásd át az összefüggéseket az edzésed, az étkezésed és az
                    eredményeid között.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="py-24 px-6">
          <div class="mx-auto max-w-7xl flex flex-col items-center text-center">

            <div class="flex flex-col items-center gap-8 mb-16">
              <div class="max-w-2xl">
                <h2 class="text-4xl font-black text-white md:text-5xl">Ketten a világ ellen</h2>
                <p class="mt-6 text-lg text-white/60">
                  Egy fiatal, ambiciózus csapat vagyunk, akik nem érik be a középszerűvel. Ketten indítottuk el a
                  FitTrack-et azzal a céllal, hogy a piac legjobb platformját hozzuk létre nektek.
                </p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-12 max-w-4xl mx-auto">

              <div class="group flex flex-col items-center text-center">
                <div
                  class="relative mb-6 h-48 w-48 overflow-hidden rounded-full border-4 border-transparent group-hover:border-primary transition-all p-1">
                  <img alt="Alapító 1"
                    class="h-full w-full rounded-full object-cover grayscale group-hover:grayscale-0 transition-all"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAAh2wlXleEXK9sxmGaVzhsMJXeaJecyfwhGr_F7aE9qGJ_UehP914nVYc2a1aiCKdAtcBnUhpTT4dltsxwC2jnHCiVvhwywv7PK9V8gK2JtyJwQb-yGr4O8mpVl8Wg7asqN8dTTjlj2i6i_obuvTJxT8hOWwvNPkwIP3NY9QvgnPne6x8LBtO82B5Z5a0MjGT30GUp9A-LxJ1p3Rb78cIKudSLcv2Ym4TH1J1Ib9WCkjRlz3SFV5G7ubOJBTtOpnymvHMX-E8CJeJN" />
                </div>
                <h4 class="text-xl font-bold text-white">Név 1</h4>
                <p class="text-sm font-medium text-primary uppercase tracking-widest">Alapító / Fejlesztő</p>
              </div>

              <div class="group flex flex-col items-center text-center">
                <div
                  class="relative mb-6 h-48 w-48 overflow-hidden rounded-full border-4 border-transparent group-hover:border-primary transition-all p-1">
                  <img alt="Alapító 2"
                    class="h-full w-full rounded-full object-cover grayscale group-hover:grayscale-0 transition-all"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCTxUiUKhhbC_9IvJmhJ9UoRGDnrCtJ2xYHsYKU9706bTHtX6nkHzpPB7EfHDuzWcKfX86OWEhB6hclOjH54mIJncqG-a2lkMiJCTzdYe13XtVayhQCmD4MPdL5TnUits_88rwFbGf8V1V1VkEz9TgMbd98Akx4F71pmKiOHc3DHbGgxkwLfjb_SmukVKsYplyV-PSGvOYplCInAd_RERo2_5MnSRa_00Qeemg10kCvxYcKn2U-IC5fK0JGTIzz-B6VI3_hb5sf_4aa" />
                </div>
                <h4 class="text-xl font-bold text-white">Név 2</h4>
                <p class="text-sm font-medium text-primary uppercase tracking-widest">Alapító / Design</p>
              </div>

            </div>
          </div>
        </section>

        <section class="bg-neutral-dark/30 py-24 px-6">
          <div class="mx-auto max-w-3xl">
            <div class="mb-16 text-center">
              <h2 class="text-4xl font-black text-white">Gyakori kérdések</h2>
              <p class="mt-4 text-white/60">Minden, amit tudni érdemes a platformunkról.</p>
            </div>

            <Accordion :items="faqItems" />

          </div>
        </section>
      </main>
    </div>
  </AppLayout>
</template>

<style scoped></style>