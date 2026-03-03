<script>
export default {
    name: 'FoodDiaryDate',
    props: {
        modelValue: {
            type: String,
            default: () => new Date().toISOString().split('T')[0]
        }
    },
    emits: ['update:modelValue'],
    computed: {
        formattedDisplayDate() {
            const [year, month, day] = this.modelValue.split('-');
            return `${month}/${day}/${year}`;
        }
    },
    methods: {
        adjustDate(days) {
            const current = new Date(this.modelValue);
            current.setDate(current.getDate() + days);
            this.emitDate(current);
            console.log(current)
        },
        jumpToToday() {
            this.emitDate(new Date());
        },
        emitDate(dateObj) {
            const yyyy = dateObj.getFullYear();
            const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
            const dd = String(dateObj.getDate()).padStart(2, '0');
            this.$emit('update:modelValue', `${yyyy}-${mm}-${dd}`);
        }
    }
}
</script>

<template>
    <div class="food-diary-picker">
        <span class="label">FOOD DIARY DATE</span>

        <div class="picker-controls">
            <button @click="adjustDate(-1)" class="circle-btn">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
            </button>

            <div class="date-display">
                <svg class="calendar-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>{{ formattedDisplayDate }}</span>
            </div>

            <button @click="adjustDate(1)" class="circle-btn">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                    <path d="M9 18l6-6-6-6" />
                </svg>
            </button>

            <button @click="jumpToToday" class="jump-link">Jump to Today</button>
        </div>
    </div>
</template>

<style scoped>
.food-diary-picker {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #607d8b;
    padding: 20px;
}

.label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1px;
    margin-bottom: 12px;
    color: #78909c;
}

.picker-controls {
    display: flex;
    align-items: center;
    gap: 12px;
}

.circle-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background-color: #e0f7f4;
    color: #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: opacity 0.2s;
}

.circle-btn:hover {
    opacity: 0.8;
}

.date-display {
    display: flex;
    align-items: center;
    padding: 8px 24px;
    border: 2px solid #10b981;
    border-radius: 30px;
    color: #10b981;
    font-weight: 600;
    font-size: 18px;
    min-width: 140px;
    justify-content: center;
}

.calendar-icon {
    margin-right: 10px;
}

.jump-link {
    background: none;
    border: none;
    color: #607d8b;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    margin-left: 8px;
}

.jump-link:hover {
    text-decoration: underline;
}
</style>