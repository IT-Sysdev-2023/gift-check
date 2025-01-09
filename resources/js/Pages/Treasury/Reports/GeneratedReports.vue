<template>
    <AuthenticatedLayout>
        <!--WARNING!
            THIS COMPONENT IS BEING USED ACCROSS DIFFERENT USERTYPES 
            DONT YOU DARE TO CHANGE SOMETHING IN THIS COMPONENT UNLESS YOU KNOW WHAT YOU'RE DOING YOU PIECE OF SH*T? 
            INGNA SA YES master! HAHAHAHA 
        -->
        <Head title="List Of Generated Reports" />
        <a-breadcrumb>
            <a-breadcrumb-item>
                <Link :href="dashboardRoute">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>Generated Reports</a-breadcrumb-item>
        </a-breadcrumb>
        <a-list
            class="demo-loadmore-list mt-10"
            :loading="initLoading"
            item-layout="horizontal"
            :data-source="files"
        >
            <template #renderItem="{ item }">
                <a-list-item>
                    <template #actions>
                        <a-button
                            type="primary"
                            @click="downloadFile(item.file)"
                        >
                            <template #icon>
                                <DownloadOutlined />
                            </template>
                            Download
                        </a-button>
                    </template>
                    <a-skeleton
                        avatar
                        :title="false"
                        :loading="!!item.loading"
                        active
                    >
                        <a-list-item-meta
                            :description="'Will be deleted in: ' + item.expiration"
                        >
                            <template #title>
                                <span>{{ item.filename }}</span>
                            </template>
                            <template #avatar>
                                <a-avatar :src="'/images/icons/' + item.icon" />
                            </template>
                        </a-list-item-meta>
                        <div>
                            <span class="text-gray-400">generated</span>
                            {{ item.generatedAt }}
                        </div>
                    </a-skeleton>
                </a-list-item>
            </template>
        </a-list>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import { ReportsGeneratedTypes } from "@/types/treasury";
import { ref, computed } from "vue";

defineProps<ReportsGeneratedTypes>();
const initLoading = ref<boolean>(false);

const dashboardRoute = computed(() => {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
});

const downloadFile = (file: string) => {
    console.log(file);
    const url = route("treasury.reports.download.gc", {
        file: file,
    });
    location.href = url;
};
</script>
<style scoped>
.demo-loadmore-list {
    min-height: 350px;
}
</style>
