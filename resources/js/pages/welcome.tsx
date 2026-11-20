import { Head, Link, usePage } from '@inertiajs/react';
import AppLogoIcon from '@/components/app-logo-icon';
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';

export default function Welcome() {
    const { auth } = usePage().props;

    return (
        <>
            <Head title="Bem-vindo" />
            <div className="flex min-h-screen flex-col items-center justify-center gap-8 bg-background p-6 text-foreground">
                <div className="flex flex-col items-center gap-4 text-center">
                    <AppLogoIcon className="size-12 fill-current" />
                    <h1 className="text-2xl font-semibold">Sistema Boate</h1>
                    <p className="max-w-sm text-sm text-muted-foreground">
                        Gestão de portaria, bar, caixa e cadastros da casa.
                    </p>
                </div>

                <nav className="flex items-center gap-3">
                    {auth.user ? (
                        <Button asChild>
                            <Link href={dashboard()}>Ir para o painel</Link>
                        </Button>
                    ) : (
                        <>
                            <Button asChild>
                                <Link href={login()}>Entrar</Link>
                            </Button>
                            <Button asChild variant="outline">
                                <Link href={register()}>Criar conta</Link>
                            </Button>
                        </>
                    )}
                </nav>
            </div>
        </>
    );
}
