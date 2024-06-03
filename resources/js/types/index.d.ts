export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};

interface SharedProps extends PageProps {
    auth: {
        user: {
            id: number,
            name: string,
            email: string
        }
    }
}

export type PageWithSharedProps = InertiaPage<SharedProps>
