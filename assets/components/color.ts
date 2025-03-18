import {TinyColor} from "@ctrl/tinycolor";

export default class Color extends TinyColor {
    public isLight(): boolean {
        return this.getLuminance() > 0.179
    }

    public getPeakColor(): string {
        return this.isLight() ? '#fff' : '#000'
    }

    public getContrastColor(): string {
        return this.isLight() ? '#000' : '#fff'
    }
}
