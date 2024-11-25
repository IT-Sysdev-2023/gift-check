<template>
    <AuthenticatedLayout>
        <Head title="List Of Generated Reports" />
        <a-breadcrumb>
            <a-breadcrumb-item>
                <Link>Home</Link>
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
                        <a-button type="primary" @click="downloadFile(item.file)">
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
                            :description="'Will be deleted: ' + item.expiration"
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
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";
import { onMounted, ref, nextTick } from "vue";

const initLoading = ref(false);

defineProps<{
    files: {
        file: string;
        filename: string;
        extension: string;
        expiration: string;
    }[];
}>();

const downloadFile = (file) => {
    const url = route("treasury.reports.download.gc", {
        file: file,
    });
    location.href = url;
}
</script>
<style scoped>
.demo-loadmore-list {
    min-height: 350px;
}
</style>
