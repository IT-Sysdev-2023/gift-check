<template>
    <div v-if="datarecords !== undefined">
        <div class="flex justify-end">
            <template v-for="(link, key) in datarecords.links" :key="`link-${key}`">
                <a-button size="small" class="mt-6" style="border-radius: 2px;"
                    :type="link.active ? 'primary' : 'default'" v-html="link.label" @click="paginate(link)" />
            </template>
        </div>
        <div class="mt-2 text-end">
            <a-typography-text>{{ `Showing ${datarecords?.from || 0} to ${datarecords?.to || 0} of
                ${datarecords?.total.toLocaleString()}
                records` }}
            </a-typography-text>
        </div>
    </div>

</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
    components: {
        Link,
    },
    props: {
        datarecords: Object,

    },
    methods: {
        paginate(link) {
            if (link.url) {
                this.$inertia.visit(link.url, {
                    preserveState: true,
                    preserveScroll: true
                })
            }
        }
    }
}
</script>
