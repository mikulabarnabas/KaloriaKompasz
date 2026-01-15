type Unit = "g" | "dkg" | "kg" | "l" | "dl" | "ml";

export type Food = {
  calorie: number;
  fat: number;
  carb: number;
  protein: number;
  pivot : {
    amount : number,
    unit : Unit
  }
};




export async function loadDiary(route : string, date : string) {
    try {
        const url = route + `${encodeURIComponent(date)}`;
        const res = await fetch(url, {
            headers: { Accept: "application/json" },
            credentials: "same-origin",
        });

        if (!res.ok) throw new Error("Failed to load diary.");

        const data = await res.json();
        return { ok: true, diary : data.diary };
    } catch (e) {
        return { ok: false, error : "Failed to load diary." };
    }
}

export function getBaseFoodMacros(food : Food) {
    const units = {'g' : 1, 'dkg' : 10, 'kg' : 1000, 'l' : 1, 'dl' : 10, 'ml' : 1000};
    const divider = units[food.pivot.unit]
    food.calorie = food.calorie / divider
    food.fat = food.fat / divider
    food.carb = food.carb / divider
    food.protein = food.protein / divider
    food.pivot.amount = food.pivot.amount / divider
    return  food
}