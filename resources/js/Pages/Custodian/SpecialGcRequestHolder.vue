<template>
    <a-card>
        <a-table :data-source="specExRecord" :columns="columns" size="small">
            <template #bodyCell="{ column, record }">
                <template v-if="column.key == 'setup'">
                    <a-button>
                        Setup
                    </a-button>
                </template>
            </template>
        </a-table>
    </a-card>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        specExRecord: Object,
        columns: Array
    }
}
</script>
