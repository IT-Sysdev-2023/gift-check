<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table bordered :data-source="record.data" :columns="columns" size="small" :pagination="false">
                <template #bodyCell="{column, record}">
                    <template v-if="column.key === 'status'">
                        <span v-if="record.spexgc_payment_stat === 'paid'">
                            <a-tag color="blue">{{ record.spexgc_payment_stat }}</a-tag>
                        </span>
                        <span v-else>
                            <a-tag >{{ record.spexgc_payment_stat }}</a-tag>
                        </span>
                    </template>
                    <template v-if="column.key === 'setup'">
                        <a-button @click="() => $inertia.get(route('accounting.payment.setup', record.spexgc_id))">
                            <template #icon>
                                <FastForwardOutlined />
                            </template>
                            Setup
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination class="mt-6" :datarecords="record" />
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
const props = defineProps({
    record: Object,
    columns: Array,
})
</script>
