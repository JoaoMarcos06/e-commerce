<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Carrinho de Compras</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
            
            <div class="col-md-12">
                <div class="product-content-right">
                    <div class="woocommerce">

                        <form action="/checkout">
                            <?php if( $error != '' ){ ?>

                                <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars( $error, ENT_COMPAT, 'UTF-8', FALSE ); ?>!
                                </div>
                            <?php } ?>


                            <table cellspacing="0" class="shop_table cart">
                                <thead>
                                    <tr>
                                        <th class="product-remove">&nbsp;</th>
                                        <th class="product-thumbnail">&nbsp;</th>
                                        <th class="product-name">Produto</th>
                                        <th class="product-price">Preço</th>
                                        <th class="product-quantity">Quantidade</th>
                                        <th class="product-subtotal">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter1=-1;  if( isset($products) && ( is_array($products) || $products instanceof Traversable ) && sizeof($products) ) foreach( $products as $key1 => $value1 ){ $counter1++; ?>

                                    <tr class="cart_item">
                                        <td class="product-remove">
                                            <a title="Remove this item" class="remove" href="/e-commerce/cart/<?php echo htmlspecialchars( $value1["idproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/remove">×</a> 
                                        </td>

                                        <td class="product-thumbnail">
                                            <a href="/e-commerce/products/<?php echo htmlspecialchars( $value1["desurl"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><img width="145" height="145" alt="poster_1_up" class="shop_thumbnail" src="<?php echo htmlspecialchars( $value1["desphoto"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"></a>
                                        </td>

                                        <td class="product-name">
                                            <a href="/e-commerce/products/<?php echo htmlspecialchars( $value1["desurl"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $value1["desproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a> 
                                        </td>

                                        <td class="product-price">
                                            <span class="amount">R$ <?php echo formatCurrency($value1["vlprice"]); ?></span> 
                                        </td>

                                        <td class="product-quantity">
                                            <div class="quantity buttons_added">
                                                <input type="button" class="minus" value="-" onclick="window.location.href = '/e-commerce/cart/<?php echo htmlspecialchars( $value1["idproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/remove'">
                                                <input type="number" size="4" class="input-text qty text" title="Qty" value="<?php echo htmlspecialchars( $value1["nrqtd"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" min="0" step="1">
                                                <input type="button" class="plus" value="+" onclick="window.location.href = '/e-commerce/cart/<?php echo htmlspecialchars( $value1["idproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/add'">
                                            </div>
                                        </td>

                                        <td class="product-subtotal">
                                            <span class="amount">R$ <?php echo formatCurrency($value1["total"]); ?></span> 
                                        </td>
                                    </tr>
                                    <?php } ?>

                                    
                                </tbody>
                            </table>

                            <div class="cart-collaterals">

                                <div class="cross-sells">

                                    <h2>Cálculo de Frete</h2>
                                    
                                    <div class="coupon">
                                        <label for="cep">CEP:</label>
                                        <input type="text" placeholder="00000-000" value="<?php echo htmlspecialchars( $cart["deszipcode"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" id="cep" class="input-text" name="zipcode">
                                        <input type="submit" formmethod="post" formaction="/e-commerce/cart/freight" value="CÁLCULAR" class="button">
                                    </div>

                                </div>

                                <div class="cart_totals ">

                                    <h2>Resumo da Compra</h2>

                                    <table cellspacing="0">
                                        <tbody>
                                            <tr class="cart-subtotal">
                                                <th>Subtotal</th>
                                                <td><span class="amount">R$ <?php echo formatCurrency($cart["vlsubtotal"]); ?></span></td>
                                            </tr>

                                            <tr class="shipping">
                                                <th>Frete</th>
                                                <td>R$ <?php echo formatCurrency($cart["vlfreight"]); ?> <small><?php if( $cart["nrdays"] > 0 ){ ?> prazo de <?php echo htmlspecialchars( $cart["nrdays"], ENT_COMPAT, 'UTF-8', FALSE ); ?> dia(s)<?php } ?></small></td>
                                            </tr>

                                            <tr class="order-total">
                                                <th>Total</th>
                                                <td><strong><span class="amount">R$ <?php echo formatCurrency($cart["vltotal"]); ?></span></strong> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            <div class="pull-right">
                                <input type="submit" value="Finalizar Compra" name="proceed" class="checkout-button button alt wc-forward">
                            </div>

                        </form>

                    </div>                        
                </div>                    
            </div>
        </div>
    </div>
</div>