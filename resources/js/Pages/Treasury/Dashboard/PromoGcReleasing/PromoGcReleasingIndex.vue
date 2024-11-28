<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route(dashboardRoute)">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <div class="flex justify-between mb-5">
                <div>
                    <a-range-picker v-model:value="form.date" />
                </div>
                <div>
                    <a-input-search
                        class="mr-1"
                        v-model:value="form.search"
                        placeholder="Search here..."
                        style="width: 300px"
                    />
                </div>
            </div>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import _ from "lodash";
import { computed, ref, watch } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";

const dashboardRoute = computed(() => {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
});

const props = defineProps<{
    desc: string;
    title: string;
    data: object;
    columns: any[];
    remainingBudget: string;
    filters: {
        search: string;
        date: string | any[];
    };
}>();

const form = {
    search: props.filters.search,
    date: props.filters.date
        ? [dayjs(props.filters.date[0]), dayjs(props.filters.date[1])]
        : [],
};
const viewRecord = async (id) => {
    try {
        const { data } = await axios.get(
            route("treasury.budget.request.view.approved", id)
        );
        // this.descriptionRecord = data;
    } finally {
        // this.showModal = true;
    }
};

watch(
    () => form, // Getter function to track the form object
    debounce(() => {
        const formattedDate = form.date
            ? form.date.map((date) => date.format("YYYY-MM-DD"))
            : [];

        router.get(
            route(route().current()),
            { ...pickBy(form), date: formattedDate },
            {
                preserveState: true,
            }
        );
    }, 600),
    { deep: true } // Options object
);
</script>
