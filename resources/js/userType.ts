import { usePage } from "@inertiajs/vue3";
import { PageWithSharedProps } from "@/types";
import { computed } from "vue";

export function UserType() {
    const page = usePage<PageWithSharedProps>().props;

    const user = page.auth.user.usertype;
    const role = page.auth.user.user_role;

    const userType = (userType: string) => user === userType;
    const userRole = (userRole: number) => role === userRole;

    return {
      admin: computed(() => userType("1")),
      treasury: computed(() => (userType("2") || userType("1")) && !userRole(2)),
      retail: computed(() => (userType("7") || userType("1")) && !userRole(7)),
      accounting: computed(() => (userType("9") || userType("1")) && !userRole(9)),
      finance: computed(() => (userType("3") || userType("1")) && !userRole(3)),
      custodian: computed(() => (userType("4") || userType("1")) && !userRole(4)),
      marketing: computed(() => (userType("6") || userType("1")) && !userRole(6)),
      iad: computed(() => (userType("10") || userType("1")) && !userRole(10)),
      retailgroup: computed(() => (userType("8") || userType("1")) && !userRole(8)),
      eod: computed(() => (userType("12") || userType("1")) && !userRole(12)),
    };
}
