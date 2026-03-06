import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';
import Button from '../Button.vue';

describe('Button.vue', () => {
    it('renders the slot content correctly', () => {
        // Mount the component and pass a slot
        const wrapper = mount(Button, {
            slots: {
                default: 'Simpan Data'
            }
        });

        // Assert that the text is rendered
        expect(wrapper.text()).toContain('Simpan Data');

        // Assert that the button is not disabled by default
        const button = wrapper.find('button');
        expect(button.attributes('disabled')).toBeUndefined();
    });

    it('shows loading text and disables the button when isLoading is true', () => {
        // Mount the component with the isLoading prop
        const wrapper = mount(Button, {
            props: {
                isLoading: true
            },
            slots: {
                default: 'Simpan Data'
            }
        });

        // Assert that the text changes to "Loading..."
        expect(wrapper.text()).toContain('Loading...');

        // Assert that the button is completely disabled
        const button = wrapper.find('button');
        expect(button.attributes('disabled')).toBeDefined();
    });
});
