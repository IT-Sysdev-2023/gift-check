<template>
  
        <!-- <span class="text-red-500 text-xs">*Required</span> -->
        <a-space v-for="(item, index) in form.denomination" :key="item.id">
            <a-form-item :name="['denomination', index, 'denomination']" :rules="{
                required: true,
                message: 'Missing Denomination',
            }">
                <span>Denomination:</span>
                <a-input-number v-model:value="item.denomination" :formatter="(value) =>
                        `₱ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
                    " :min="0" style="width: auto" />
            </a-form-item>
            <a-form-item :name="['denomination', index, 'qty']" :rules="{
                required: true,
                message: 'Missing Quantity',
            }">
                <span>Quantity:</span>
                <a-input-number v-model:value="item.qty" style="width: auto" :min="0" />
            </a-form-item>
            <MinusCircleOutlined @click="removeUser(item)" />
        </a-space>
        <a-form-item>
            <a-button type="dashed" block @click="addDenom">
                <PlusOutlined />
                Add Denomination
            </a-button>
        </a-form-item>
        <span v-if="form.errors.denomination" class="text-red-500">{{
            form.errors.denomination
            }}</span>
        <a-form-item label="Total:" name="denomTotal">
            <a-input-number class="p-2 text-2xl" size="large" :value="totalDenomination" :formatter="(value) =>
                    `₱ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
                " :min="0" style="width: 100%" readonly />
        </a-form-item>

</template>
<script lang="ts" setup>
import { computed } from "vue";
interface Denom {
    denomination: number;
    qty: number;
    id: number;
}
const props = defineProps<{
    form: any;
}>();

const totalDenomination = computed(() => {
    return props.form.denomination.reduce((acc, item) => {
        return acc + (item.denomination * item.qty);
    }, 0);
});

const removeUser = (item: Denom) => {
    const index = props.form.denomination.indexOf(item);
    if (index !== -1) {
        props.form.denomination.splice(index, 1);
    }
};
const addDenom = () => {
    props.form.denomination.push({
        denomination: 0,
        qty: 0,
        id: Date.now(),
    });
};
</script>
