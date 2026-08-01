const clamp = (value, min, max) => Math.min(max, Math.max(min, Number(value)));

const freshTransform = () => ({ x: 50, y: 50, scale: 1, rotation: 0 });

export default function productStudio(config) {
    return {
        catalog: config.catalog,
        geometry: config.geometry,
        assetBase: config.assetBase,
        productType: config.initialType,
        activeSide: 'front',
        activeTab: 'design',
        previewColor: config.initialPreviewColor || 'White',
        previewSide: 'front',
        title: config.initialTitle || '',
        tags: config.initialTags || '',
        files: { front: null, back: null },
        previews: { front: null, back: null },
        transforms: { front: freshTransform(), back: freshTransform() },
        artworkSelected: false,
        isFlipping: false,
        submitting: false,
        rightsAccepted: false,
        dirty: false,
        fileError: '',
        sheetOpen: true,
        spacePressed: false,
        interaction: null,
        viewport: { zoom: 1, pan: { x: 0, y: 0 } },
        viewportPointers: {},

        get typeConfig() {
            return this.catalog[this.productType];
        },

        get typeColors() {
            return this.typeConfig.colors || ['White', 'Black', 'Gray'];
        },

        get activeTransform() {
            return this.transforms[this.activeSide];
        },

        get hasArtwork() {
            return Boolean(this.files[this.activeSide]);
        },

        get hasAnyArtwork() {
            return Boolean(this.files.front || this.files.back);
        },

        get artworkUrl() {
            return this.previews[this.activeSide];
        },

        get mockupUrl() {
            return `${this.assetBase}/${this.productType}/${this.previewColor.toLowerCase()}-${this.productType}-${this.activeSide}.png`;
        },

        get tabIndex() {
            return ['design', 'product', 'publish'].indexOf(this.activeTab);
        },

        get canPublish() {
            return Boolean(
                this.hasAnyArtwork
                && this.title.trim()
                && this.tags.trim()
                && this.rightsAccepted
            );
        },

        get stageStyle() {
            return {
                transform: `translate3d(${this.viewport.pan.x}px, ${this.viewport.pan.y}px, 0) scale(${this.viewport.zoom}) rotateY(${this.isFlipping ? 180 : 0}deg)`,
            };
        },

        get printAreaStyle() {
            const position = this.geometry[this.productType][this.activeSide];
            return {
                left: `${position.x}%`,
                top: `${position.y}%`,
                width: `${position.w}%`,
                height: `${position.h}%`,
            };
        },

        get artworkStyle() {
            const transform = this.activeTransform;
            return {
                left: `${transform.x}%`,
                top: `${transform.y}%`,
                transform: `translate(-50%, -50%) rotate(${transform.rotation}deg) scale(${transform.scale})`,
            };
        },

        init() {
            if (!this.typeColors.includes(this.previewColor)) this.previewColor = this.typeColors[0];
            if (window.innerWidth < 768) this.sheetOpen = false;
            this.onGlobalKeydown = (event) => {
                if (event.code === 'Space' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(event.target.tagName)) {
                    event.preventDefault();
                    this.spacePressed = true;
                }
                if ((event.key === 'Delete' || event.key === 'Backspace')
                    && this.artworkSelected
                    && this.files[this.activeSide]
                    && !['INPUT', 'TEXTAREA', 'SELECT'].includes(event.target.tagName)
                    && !event.target.isContentEditable) {
                    event.preventDefault();
                    this.removeArtwork();
                }
            };
            this.onGlobalKeyup = (event) => {
                if (event.code === 'Space') this.spacePressed = false;
            };
            this.onBeforeUnload = (event) => {
                if (this.dirty && !this.submitting) {
                    event.preventDefault();
                    event.returnValue = '';
                }
            };
            window.addEventListener('keydown', this.onGlobalKeydown);
            window.addEventListener('keyup', this.onGlobalKeyup);
            window.addEventListener('beforeunload', this.onBeforeUnload);
        },

        destroy() {
            window.removeEventListener('keydown', this.onGlobalKeydown);
            window.removeEventListener('keyup', this.onGlobalKeyup);
            window.removeEventListener('beforeunload', this.onBeforeUnload);
            Object.values(this.previews).forEach((url) => url && URL.revokeObjectURL(url));
        },

        markDirty() {
            this.dirty = true;
        },

        setTab(tab) {
            this.activeTab = tab;
            if (window.innerWidth < 768) this.sheetOpen = true;
        },

        nextTab() {
            if (this.activeTab === 'design') {
                if (!this.hasAnyArtwork) {
                    this.fileError = 'Upload artwork on the front or back before continuing.';
                    this.$refs.frontFile?.focus();
                    return;
                }
                this.setTab('product');
                return;
            }

            if (this.activeTab === 'product') {
                if (!this.title.trim() || !this.tags.trim()) {
                    const missingField = !this.title.trim() ? 'title' : 'tags';
                    this.$refs.form.querySelector(`[name="${missingField}"]`)?.reportValidity();
                    return;
                }
                this.setTab('publish');
            }
        },

        previousTab() {
            if (this.activeTab === 'publish') this.setTab('product');
            else if (this.activeTab === 'product') this.setTab('design');
        },

        setProductType(type) {
            if (this.catalog[type]) {
                this.productType = type;
                if (!this.typeColors.includes(this.previewColor)) this.previewColor = this.typeColors[0];
                this.markDirty();
            }
        },

        setSide(side) {
            this.activeSide = side;
            this.artworkSelected = this.hasArtwork;
        },

        flipSide() {
            if (this.isFlipping) return;
            const nextSide = this.activeSide === 'front' ? 'back' : 'front';
            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reducedMotion) {
                this.setSide(nextSide);
                return;
            }
            this.isFlipping = true;
            window.setTimeout(() => this.setSide(nextSide), 160);
            window.setTimeout(() => { this.isFlipping = false; }, 340);
        },

        openFilePicker() {
            const input = this.activeSide === 'front' ? this.$refs.frontFile : this.$refs.backFile;
            input?.click();
        },

        chooseFile(side, event) {
            const file = event.target.files?.[0];
            if (!file) return;
            this.fileError = '';
            if (!file.type.startsWith('image/')) {
                this.fileError = 'Choose a PNG, JPG, or WebP image.';
                event.target.value = '';
                return;
            }
            if (file.size > 8 * 1024 * 1024) {
                this.fileError = 'Images must be smaller than 8 MB.';
                event.target.value = '';
                return;
            }
            if (this.previews[side]) URL.revokeObjectURL(this.previews[side]);
            this.files[side] = file;
            this.previews[side] = URL.createObjectURL(file);
            if (side === 'front') this.previewSide = 'front';
            this.activeSide = side;
            this.artworkSelected = true;
            this.markDirty();
        },

        removeArtwork() {
            const side = this.activeSide;
            if (!this.files[side]) return;
            if (this.previews[side]) URL.revokeObjectURL(this.previews[side]);
            this.files[side] = null;
            this.previews[side] = null;
            const input = side === 'front' ? this.$refs.frontFile : this.$refs.backFile;
            if (input) input.value = '';
            if (this.previewSide === side) this.previewSide = this.files.front ? 'front' : 'back';
            this.artworkSelected = false;
            this.markDirty();
        },

        resetArtwork() {
            this.transforms[this.activeSide] = freshTransform();
            this.artworkSelected = true;
            this.markDirty();
        },

        startArtworkInteraction(event, mode, handle = null) {
            if (event.button !== undefined && event.button !== 0) return;
            const rect = this.$refs.printArea.getBoundingClientRect();
            const transform = { ...this.activeTransform };
            const center = {
                x: rect.left + (rect.width * transform.x / 100),
                y: rect.top + (rect.height * transform.y / 100),
            };
            this.artworkSelected = true;
            this.interaction = {
                mode, handle, side: this.activeSide, rect, transform, center,
                startX: event.clientX, startY: event.clientY,
                startAngle: Math.atan2(event.clientY - center.y, event.clientX - center.x) * 180 / Math.PI,
            };
            this._pointerMove = (moveEvent) => this.updateArtworkInteraction(moveEvent);
            this._pointerUp = () => this.endArtworkInteraction();
            window.addEventListener('pointermove', this._pointerMove);
            window.addEventListener('pointerup', this._pointerUp, { once: true });
            event.currentTarget?.setPointerCapture?.(event.pointerId);
        },

        updateArtworkInteraction(event) {
            const interaction = this.interaction;
            if (!interaction) return;
            const transform = this.transforms[interaction.side];
            const dx = event.clientX - interaction.startX;
            const dy = event.clientY - interaction.startY;
            if (interaction.mode === 'drag') {
                transform.x = clamp(interaction.transform.x + (dx / interaction.rect.width) * 100, 0, 100);
                transform.y = clamp(interaction.transform.y + (dy / interaction.rect.height) * 100, 0, 100);
            } else if (interaction.mode === 'resize') {
                const horizontal = interaction.handle.includes('e') ? dx : -dx;
                const vertical = interaction.handle.includes('s') ? dy : -dy;
                const delta = (horizontal + vertical) / 2;
                transform.scale = clamp(interaction.transform.scale + delta / Math.min(interaction.rect.width, interaction.rect.height), 0.25, 2);
            } else if (interaction.mode === 'rotate') {
                const angle = Math.atan2(event.clientY - interaction.center.y, event.clientX - interaction.center.x) * 180 / Math.PI;
                transform.rotation = clamp(interaction.transform.rotation + angle - interaction.startAngle, -180, 180);
            }
            this.markDirty();
        },

        endArtworkInteraction() {
            if (this._pointerMove) window.removeEventListener('pointermove', this._pointerMove);
            this.interaction = null;
        },

        nudgeArtwork(event) {
            if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(event.key)) return;
            event.preventDefault();
            const step = event.shiftKey ? 10 : 1;
            const transform = this.activeTransform;
            if (event.key === 'ArrowUp') transform.y = clamp(transform.y - step, 0, 100);
            if (event.key === 'ArrowDown') transform.y = clamp(transform.y + step, 0, 100);
            if (event.key === 'ArrowLeft') transform.x = clamp(transform.x - step, 0, 100);
            if (event.key === 'ArrowRight') transform.x = clamp(transform.x + step, 0, 100);
            this.markDirty();
        },

        startViewportPan(event) {
            if (event.target.closest('.studio-artwork')) return;
            if (!this.spacePressed && event.pointerType !== 'touch') return;
            this.viewportPointers[event.pointerId] = { x: event.clientX, y: event.clientY };
            this.$refs.viewport.setPointerCapture?.(event.pointerId);
        },

        moveViewportPan(event) {
            const pointer = this.viewportPointers[event.pointerId];
            if (!pointer) return;
            const ids = Object.keys(this.viewportPointers);
            if (ids.length < 2 && !this.spacePressed && event.pointerType === 'touch') return;
            this.viewport.pan.x += event.clientX - pointer.x;
            this.viewport.pan.y += event.clientY - pointer.y;
            pointer.x = event.clientX;
            pointer.y = event.clientY;
        },

        endViewportPan(event) {
            delete this.viewportPointers[event.pointerId];
        },

        zoomViewport(event) {
            this.viewport.zoom = clamp(this.viewport.zoom + (event.deltaY > 0 ? -0.08 : 0.08), 0.5, 2.5);
        },

        zoomIn() { this.viewport.zoom = clamp(this.viewport.zoom + 0.1, 0.5, 2.5); },
        zoomOut() { this.viewport.zoom = clamp(this.viewport.zoom - 0.1, 0.5, 2.5); },
        resetViewport() { this.viewport = { zoom: 1, pan: { x: 0, y: 0 } }; },

        toggleSheet() {
            this.sheetOpen = !this.sheetOpen;
        },

        submitForm(event) {
            if (!this.canPublish) {
                event.preventDefault();
                if (!this.hasAnyArtwork) {
                    this.fileError = 'Upload artwork on the front or back before publishing.';
                    this.setTab('design');
                } else if (!this.title.trim() || !this.tags.trim()) {
                    this.setTab('product');
                } else {
                    this.setTab('publish');
                }
                return;
            }
            this.submitting = true;
            this.dirty = false;
        },
    };
}
