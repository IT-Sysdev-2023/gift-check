<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <div class="flex justify-between mb-5">
                <div>
                    <!-- <a-range-picker v-model:value="form.date" /> -->
                </div>
                <div>
                    <!-- <a-input-search
                    class="mr-1"
                    v-model:value="form.search"
                    placeholder="Search here..."
                    style="width: 300px"
                /> -->
                </div>
            </div>
            <a-typography-title :level="4">{{ title }}</a-typography-title>

            <a-tabs v-model:activeKey="activeKey" @change="onTabChange">
                <a-tab-pane key="*" tab="Internal"> </a-tab-pane>
                <a-tab-pane key="0" tab="External" force-render> </a-tab-pane>

            </a-tabs>
            <reviewed-gc-releasing :loading="onLoading" :records="records" :columns="columns" @view-record="viewRecord"/>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/core";
import { ref } from "vue";
import { route } from 'ziggy-js';
const props = defineProps<{
    title: string;
    records: {
        data: any[];
    };
    tab: string;
    columns: any[];
}>();
const onLoading= ref(false);
const activeKey = ref(props.tab);
const viewRecord = async (id: number) => {
    router.get(route("treasury.special.gc.viewReleasing", id));
};

const onTabChange = (val: string) => {
    router.visit(route(route().current()), {
        data: { promo: val },
        only: ["records", "tab"],
        preserveScroll: true,
        onStart: () =>{
            onLoading.value = true;
        },
        onSuccess: () => {
            onLoading.value = false;
        }
    });
};
</script>

<style lang="scss" scoped></style>
