export function computeTotals(lines, discount = 0)
{
	const subtotal = lines.reduce((s, l)=> s + (l.unit_price * l.quantity), 0)

	const totalVat = lines.reduce((s, l) => {

		const line = l.unit_price + l.quantity
		return s + (line * (l.vat_percent || 0)/100)
	}, 0)

	const grand = Math.max(0, subtotal + totalVat - (discount || 0))

	return {

		subtotal: round2(subtotal),
		totalVat: round2(totalVat),
		grand: round2(grand)
	}
}

export const round2 = (n) => Math.round((n + Number.EPSILON) * 100)/100