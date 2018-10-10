<?php
/* @var $products array*/
?>
<html>
<head>
    Testing view creating
</head>
<body>
<h1>First View!</h1>
<table>
    <thead>
    <tr>
        <th>Product Code</th>
        <th>Product Name</th>
        <th>Product Description</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product){
            /* @var $product \App\Entity\ProductData*/?>
            <tr>
                <td><?php echo $product->getStrProductCode();?></td>
                <td><?php echo $product->getStrProductName();?></td>
                <td><?php echo $product->getStrProductDesc();?></td>
            </tr>
        <?php }?>
</table>
</body>
</html>
