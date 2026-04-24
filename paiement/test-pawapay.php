<form method="POST" action="pawapay-init.php">
    <input type="hidden" name="reservation_id" value="1">

    <label>Montant</label>
    <input type="number" name="amount" value="1000" required>

    <label>Téléphone</label>
    <input type="text" name="phone" placeholder="2376XXXXXXXX">

    <button type="submit">Payer avec pawaPay</button>
</form>