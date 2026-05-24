const { test, expect } = require('@playwright/test');

test.describe('Crop selection interactions', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('http://127.0.0.1:8000/predictions/create');
    await page.waitForLoadState('networkidle');
  });

  test('select suggestion by mouse click populates hidden input and shows card', async ({ page }) => {
    const input = page.locator('input[placeholder*="Search crop"]').first();
    await input.fill('Maize');
    await page.waitForTimeout(300);

    const suggestion = page.locator('li', { hasText: 'Maize' }).first();
    await expect(suggestion).toBeVisible();
    // Emulate pointerdown then click (mobile/desktop robust)
    await suggestion.dispatchEvent('pointerdown');
    await suggestion.click();

    // Hidden input should be updated or cleared for persisted crops
    const hidden = page.locator('#crop-name-hidden');
    await expect(hidden).toBeVisible();

    // Exactly one temporary/persistent card should be visible
    const visibleCards = await page.locator('.crop-card-label').filter({ has: page.locator('p', { hasText: 'Maize' }) }).count();
    expect(visibleCards).toBeGreaterThan(0);

    // Dropdown should be hidden
    await expect(page.locator('#crop-suggestions-list')).toBeHidden();
  });

  test('select suggestion by keyboard Enter works', async ({ page }) => {
    const input = page.locator('input[placeholder*="Search crop"]').first();
    await input.fill('Tomato');
    await page.waitForTimeout(300);
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');

    await expect(page.locator('#crop-suggestions-list')).toBeHidden();
    const hidden = page.locator('#crop-name-hidden');
    await expect(hidden).toBeVisible();
  });

  test('select suggestion by touch pointerdown works', async ({ page }) => {
    const input = page.locator('input[placeholder*="Search crop"]').first();
    await input.fill('Potato');
    await page.waitForTimeout(300);
    const suggestion = page.locator('li', { hasText: 'Potato' }).first();
    await suggestion.dispatchEvent('pointerdown', { pointerType: 'touch' });
    await suggestion.click();

    await expect(page.locator('#crop-suggestions-list')).toBeHidden();
    const hidden = page.locator('#crop-name-hidden');
    await expect(hidden).toBeVisible();
  });
});
