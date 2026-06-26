<x-layouts.app>
    <x-slot:title>About Us — Frost Layouts</x-slot:title>

    <section class="max-w-4xl mx-auto px-fluid-sm py-fluid-md">
        <span class="text-xs font-bold uppercase tracking-widest text-frost-muted">Who we are</span>
        <h1 class="text-3xl md:text-5xl font-bold tracking-tight mt-2 mb-6">We focus strictly on the structural layout.</h1>

        <p class="text-base text-frost-dark mb-6 leading-relaxed">
            Frost was conceptualized to strip away the noise of complex modern designs. By relying strictly on robust block distributions and black-and-white accents, user attention converges safely on the value proposition.
        </p>

        <x-frost.quote author="Frost Design System Team">
            Design is the foundation on which aesthetics, user experience, and baseline functionality is built.
        </x-frost.quote>

        <p class="text-base text-frost-dark mt-6 mb-12 leading-relaxed">
            Our framework makes it incredibly simple to orchestrate portfolios, showcase creative work, or deliver high-performance commercial storefront workflows with extreme visual predictability.
        </p>

        <div class="border-t border-frost-border pt-12">
            <h2 class="text-xl font-bold tracking-tight mb-8">Team Members</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-frost-dark rounded-full flex-shrink-0"></div>
                    <div>
                        <h4 class="font-bold text-sm">Urano Gonzalez</h4>
                        <p class="text-xs text-frost-muted">Lead Technical Architect</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-neutral-200 rounded-full flex-shrink-0"></div>
                    <div>
                        <h4 class="font-bold text-sm">WP Engine Frost</h4>
                        <p class="text-xs text-frost-muted">Original Design Concept</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-frost.newsletter />
</x-layouts.app>