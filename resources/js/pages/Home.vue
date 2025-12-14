<script setup>
import Button from "primevue/button";
import Card from "primevue/card";
import Slider from 'primevue/slider';

import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';


function toggleDarkMode() {
    document.documentElement.classList.toggle('my-app-dark');
}
</script>

<template>
  <Button label="Toggle Dark Mode" @click="toggleDarkMode()" />
  <div class="grid grid-cols-3 gap-8">
    <Card class="">
      <template #header>
        <div class="flex justify-center pt-3">
          <i class="fa-solid fa-bowl-food text-6xl text-brand"></i>
        </div>
      </template>

      <template #title>
        <div class="text-center font-semibold">Ételnapló</div>
      </template>

      <template #subtitle>
        <div class="text-center">Card subtitle</div>
      </template>

      <template #content>
        <p class="m-0">
          Lorem ipsum dolor sit amet, consectetur adipisicing elit. Inventore sed
          consequuntur error repudiandae numquam deserunt quisquam repellat libero
          asperiores earum nam nobis, culpa ratione quam perferendis esse,
          cupiditate neque quas!
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button label="Cancel" severity="secondary" variant="outlined" class="w-full" />
        </div>
      </template>
    </Card>
    <Card class="">
      <template #header>
        <div class="flex justify-center pt-3">
          <i class="fa-solid fa-bowl-food text-6xl text-brand"></i>
        </div>
      </template>

      <template #title>
        <div class="text-center font-semibold">Edzésnapló</div>
      </template>

      <template #subtitle>
        <div class="text-center">Card subtitle</div>
      </template>

      <template #content>
        <p class="m-0">
          Lorem ipsum dolor sit amet, consectetur adipisicing elit. Inventore sed
          consequuntur error repudiandae numquam deserunt quisquam repellat libero
          asperiores earum nam nobis, culpa ratione quam perferendis esse,
          cupiditate neque quas!
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button label="Cancel" severity="secondary" variant="outlined" class="w-full" />
        </div>
      </template>
    </Card>
    <Card class="">
      <template #header>
        <div class="flex justify-center pt-3">
          <i class="fa-solid fa-bowl-food text-6xl text-brand"></i>
        </div>
      </template>

      <template #title>
        <div class="text-center font-semibold">Statisztika</div>
      </template>

      <template #subtitle>
        <div class="text-center">Card subtitle</div>
      </template>

      <template #content>
        <p class="m-0">
          Lorem ipsum dolor sit amet, consectetur adipisicing elit. Inventore sed
          consequuntur error repudiandae numquam deserunt quisquam repellat libero
          asperiores earum nam nobis, culpa ratione quam perferendis esse,
          cupiditate neque quas!
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button label="Cancel" severity="secondary" variant="outlined" class="w-full" />
        </div>
      </template>
    </Card>
  </div>
  <Accordion value="0">
    <AccordionPanel value="0">
        <AccordionHeader>Header I</AccordionHeader>
        <AccordionContent>
            <p class="m-0">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>
        </AccordionContent>
    </AccordionPanel>
    <AccordionPanel value="1">
        <AccordionHeader>Header II</AccordionHeader>
        <AccordionContent>
            <p class="m-0">
                Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim
                ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Consectetur, adipisci velit, sed quia non numquam eius modi.
            </p>
        </AccordionContent>
    </AccordionPanel>
    <AccordionPanel value="2">
        <AccordionHeader>Header III</AccordionHeader>
        <AccordionContent>
            <p class="m-0">
                At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa
                qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus.
            </p>
        </AccordionContent>
    </AccordionPanel>
</Accordion>
</template>

<style scoped></style>